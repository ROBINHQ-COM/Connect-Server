<?php


namespace App\Robin;


use Carbon\Carbon;
use Illuminate\Http\Response;
use Robin\Api\Collections\Customers;
use Robin\Api\Collections\Orders;
use Robin\Api\Exceptions\RobinSendFailedException;
use Robin\Api\Logger\RobinLogger;
use Robin\Api\Models\Views\ListView;
use Robin\Api\Models\Views\Panel;
use Robin\Api\Robin;
use Robin\Connect\SEOShop\Details\DetailViewMaker;
use Robin\Connect\SEOShop\Hooks\Format;
use Robin\Connect\SEOShop\Hooks\Hook;
use Robin\Connect\SEOShop\Hooks\ItemAction;
use Robin\Connect\SEOShop\Hooks\ItemGroup;
use Robin\Connect\SEOShop\Hooks\Status;
use Robin\Connect\SEOShop\Models\Customer;
use Robin\Connect\SEOShop\Models\Order;
use Robin\Connect\SEOShop\SEOShop;

class Connect
{
    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var Robin
     */
    private $robin;
    /**
     * @var Order
     */
    private $order;
    /**
     * @var RobinLogger
     */
    private $robinLogger;
    /**
     * @var SEOShop
     */
    private $SEOShop;

    /**
     * @param Customer $customer
     * @param Order $order
     * @param Robin $robin
     * @param RobinLogger $robinLogger
     * @param SEOShop $SEOShop
     */
    public function __construct(
        Customer $customer,
        Order $order,
        Robin $robin,
        RobinLogger $robinLogger,
        SEOShop $SEOShop
    ) {
        $this->customer = $customer;
        $this->robin = $robin;
        $this->order = $order;
        $this->robinLogger = $robinLogger;
        $this->SEOShop = $SEOShop;
    }

    public function on()
    {
        $client = $this->SEOShop->getApi();
        $hooks = $client->webhooks->get();
        if (!$this->containsOrdersHook($hooks)) {
            $hooks[] = $client->webhooks->create($this->getOrdersHook());
        }
        if (!$this->containsCustomersHook($hooks)) {
            $hooks[] = $client->webhooks->create($this->getCustomersHook());
        }

        $count = $client->webhooks->count();
        return new Response(['num_hooks' => $count, 'hooks' => $hooks], 201);
    }

    public function off()
    {
        $client = $this->SEOShop->getApi();
        $count = 0;
        $hooks = collect($client->webhooks->get());

        foreach ($hooks as $hook) {
            if ($this->isRobinHook($hook)) {
                $client->webhooks->delete($hook['id']);
                $count++;
            }
        }

        return new Response(['num_hooks' => $hooks->count() - $count], 200);
    }

    /**
     * @param $json
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function customers($json)
    {
        $customer = $this->customer->makeFromArray($json);

        $customers = Customers::make(
            [
                $this->makeRobinCustomer($customer)
            ]
        );

        try {
            return $this->robin->customers($customers);
        } catch (RobinSendFailedException $exception) {
            $this->error($customers, $exception, $customer);
            return new Response("", 500);
        }
    }

    /**
     * @param $json
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function orders($json)
    {
        $order = $this->order->makeFromArray($json);
        $createdAt = $order->createdAt;
        $listView = ListView::make($order->number, $createdAt, $order->status);
        $orders = Orders::make(
            [
                \Robin\Api\Models\Order::make(
                    $order->number,
                    $order->email,
                    $createdAt,
                    $order->priceIncl,
                    $order->getEditUrl(),
                    $listView,
                    DetailViewMaker::makeDetailViews($order)
                )
            ]
        );

        return $this->robin->orders($orders);
    }

    public function healthCheck()
    {
        try {
            $hooks = collect($this->SEOShop->getApi()->webhooks->get());
            $this->robin->orders(new Orders());
            $this->robin->customers(new Customers());
        } catch (\Exception $exception) {
            return "Something is wrong! " . $exception->getMessage();
        }

        return "Everything is operating normally. You have " . $hooks->count() . " webhooks registered";
    }

    /**
     * @param Customer $customer
     * @return \Robin\Api\Models\Customer
     */
    private function makeRobinCustomer(Customer $customer)
    {
        $createdAt = $customer->createdAt;
        $panelView = Panel::make($customer->orders->count, $customer->orders->total);
        return \Robin\Api\Models\Customer::make(
            $customer->email,
            $createdAt,
            $customer->orders->count,
            $customer->orders->totalSpendFormatted(),
            $panelView
        );
    }

    /**
     * @param $customers
     * @param $exception
     * @param $customer
     */
    private function error($customers, RobinSendFailedException $exception, $customer)
    {
        $this->robinLogger->sendError(
            $customers,
            $exception->getResponse()->getReasonPhrase(),
            $exception->getMessage(),
            $customer
        );
    }

    /**
     * @return array
     */
    private function getOrdersHook()
    {
        $urlOrders = env("HOOK_BASE_URL") . '/hooks/orders';
        $orders = new Hook(
            $urlOrders, Format::JSON, Status::ACTIVE, ItemAction::ALL,
            ItemGroup::ORDERS, 'NL'
        );
        return $orders->toArray();
    }

    /**
     * @return array
     */
    private function getCustomersHook()
    {
        $urlCustomers = env("HOOK_BASE_URL") . '/hooks/customers';
        $customers = new Hook(
            $urlCustomers, Format::JSON, Status::ACTIVE, ItemAction::ALL, ItemGroup::CUSTOMERS, "NL"
        );
        return $customers->toArray();
    }

    public function getHooksCount()
    {
        return $this->SEOShop->getApi()->webhooks->count();
    }

    /**
     * @param $hook
     * @return bool
     */
    private function isRobinHook($hook)
    {
        return $this->isOrdersHook($hook) || $this->isCustomersHook($hook);
    }

    /**
     * @param $hook
     * @return bool
     */
    private function isOrdersHook($hook)
    {
        return $hook['address'] == env("HOOK_BASE_URL") . '/hooks/orders';
    }

    /**
     * @param $hook
     * @return bool
     */
    private function isCustomersHook($hook)
    {
        return $hook['address'] == env("HOOK_BASE_URL") . '/hooks/customers';
    }

    private function containsOrdersHook($hooks)
    {
        foreach ($hooks as $hook) {
            if ($this->isOrdersHook($hook)) {
                return true;
            }
        }
        return false;
    }

    private function containsCustomersHook($hooks)
    {
        foreach ($hooks as $hook) {
            if ($this->isCustomersHook($hook)) {
                return true;
            }
        }
        return false;
    }

}