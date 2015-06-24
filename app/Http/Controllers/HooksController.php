<?php namespace App\Http\Controllers;

use FastRoute\Dispatcher;
use Illuminate\Bus\BusServiceProvider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Laravel\Lumen\Application;
use Robin\Connect\Commands\Customers\Handlers\SendCustomerToRobinHandler;
use Robin\Connect\Commands\Customers\SendCustomerToRobin;
use Robin\Connect\Commands\Orders\Handlers\SendOrderToRobinHandler;
use Robin\Connect\Commands\Orders\SendOrderToRobin;
use Robin\Connect\SEOShop\Api\Client;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Robin\Connect\SEOShop\Hooks\Format;
use Robin\Connect\SEOShop\Hooks\Hook;
use Robin\Connect\SEOShop\Hooks\ItemAction;
use Robin\Connect\SEOShop\Hooks\ItemGroup;
use Robin\Connect\SEOShop\Hooks\Status;

class HooksController extends BaseController
{

    public function customers(Request $request, Client $seoShop, \Robin\Connect\Robin\Api\Client $robin)
    {

        $json = $request->json()->all();
        $command = new SendCustomerToRobin($json, $seoShop, $robin);
        $handler = new SendCustomerToRobinHandler();

        return $handler->handle($command);
    }

    public function orders(Request $request, Client $seoShop, \Robin\Connect\Robin\Api\Client $robin)
    {
        $json = $request->json()->all();
        $command = new SendOrderToRobin($json, $seoShop, $robin);
        $handler = new SendOrderToRobinHandler();

        return $handler->handle($command);
    }

    public function unregister(\WebshopappApiClient $client)
    {

        $count = $client->webhooks->count();
        $hooks = $client->webhooks->get();

        foreach ($hooks as $hook) {
            $client->webhooks->delete($hook['id']);
        }

        return new Response(['num_hooks_deleted' => $count], 200);
    }

    public function register(\WebshopappApiClient $client)
    {
        $hooks = [];
        $hooks[] = $client->webhooks->create($this->getOrdersHook());
        $hooks[] = $client->webhooks->create($this->getCustomersHook());

        $count = $client->webhooks->count();
        return new Response(['num_hooks_created' => $count, 'hooks' => $hooks], 201);
    }

    /**
     * @return array
     */
    private function getOrdersHook()
    {
        $urlOrders = "http://seoshop.megawubs.ultrahook.com/hooks/orders/";
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
        $urlCustomers = "http://seoshop.megawubs.ultrahook.com/hooks/customers/";
        $customers = new Hook(
            $urlCustomers, Format::JSON, Status::ACTIVE, ItemAction::ALL, ItemGroup::CUSTOMERS, "NL"
        );
        return $customers->toArray();
    }
}
