<?php namespace App\Http\Controllers;

use App\Robin\Connect;
use Carbon\Carbon;
use FastRoute\Dispatcher;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Robin\Api\Collections\Customers;
use Robin\Api\Collections\Orders;
use Robin\Api\Exceptions\RobinException;
use Robin\Api\Logger\RobinLogger;
use Robin\Api\Models\Views\ListView;
use Robin\Api\Models\Views\Panel;
use Robin\Api\Robin;
use Robin\Connect\Commands\Customers\Handlers\SendCustomerToRobinHandler;
use Robin\Connect\Commands\Customers\SendCustomerToRobin;
use Robin\Connect\Commands\Orders\Handlers\SendOrderToRobinHandler;
use Robin\Connect\Commands\Orders\SendOrderToRobin;
use Robin\Connect\Contracts\Retriever;
use Robin\Connect\Contracts\Sender;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Robin\Connect\SEOShop\Api\Client;
use Robin\Connect\SEOShop\Details\DetailViewMaker;
use Robin\Connect\SEOShop\Hooks\Format;
use Robin\Connect\SEOShop\Hooks\Hook;
use Robin\Connect\SEOShop\Hooks\ItemAction;
use Robin\Connect\SEOShop\Hooks\ItemGroup;
use Robin\Connect\SEOShop\Hooks\Status;
use Robin\Connect\SEOShop\Models\Customer;
use Robin\Connect\SEOShop\Models\Order;
use Robin\Connect\SEOShop\SEOShop;

class HooksController extends BaseController
{

    public function customers(Request $request, Connect $connect)
    {
        $json = $request->json()->all();
        $response = $connect->customers($json);

        return new Response("", $response->getStatusCode());
    }

    public function orders(Request $request, Connect $connect)
    {
        $json = $request->json()->all();
        $response = $connect->orders($json);

        return new Response("", $response->getStatusCode());
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

    public function register(Connect $connect)
    {
        return $connect->register();
    }
}
