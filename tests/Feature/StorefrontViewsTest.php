<?php

namespace Tests\Feature;

use App\Http\Controllers\UserOrderController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class StorefrontViewsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        // These rendering tests must never connect to the store's database.
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'session.driver' => 'array',
            'services.midtrans.is_production' => false,
            'services.midtrans.client_key' => 'render-test-client-key',
        ]);
        $this->app['view']->share('errors', new ViewErrorBag);
    }

    public function test_guests_can_open_auth_forms_and_protected_pages_require_login(): void
    {
        $this->get(route('login'))->assertOk()
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="_token"', false);

        $this->get(route('register'))->assertOk()
            ->assertSee('name="name"', false)
            ->assertSee('name="address"', false)
            ->assertSee('name="password_confirmation"', false);

        foreach (['cart.index', 'checkout.index', 'user.orders', 'user.history'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }

        $this->get('/orders/123')->assertRedirect(route('login'));
    }

    public function test_storefront_cards_preserve_product_data_and_login_flow(): void
    {
        $product = $this->product();

        $this->view('shop.index', ['products' => new Collection([$product])])
            ->assertSee($product->name)
            ->assertSee('25.000')
            ->assertSee(route('login'), false)
            ->assertDontSee('action="'.route('cart.add.ajax', $product->getKey()).'"', false);

        $this->actingAs($this->customer());

        $this->view('shop.index', ['products' => new Collection([$product])])
            ->assertSee('add-to-cart-form', false)
            ->assertSee('action="'.route('cart.add.ajax', $product->getKey()).'"', false)
            ->assertSee('name="quantity"', false)
            ->assertSee('max="12"', false)
            ->assertSee('name="csrf-token"', false);
    }

    public function test_catalog_preserves_search_pagination_and_escapes_product_names(): void
    {
        $product = $this->product();
        $product->name = '<script>alert("product")</script>';
        $products = new LengthAwarePaginator([$product], 25, 12, 1, [
            'path' => route('shop.products'),
            'query' => ['search' => 'beras'],
        ]);

        $this->view('shop.products', ['products' => $products, 'search' => 'beras'])
            ->assertSee($product->name)
            ->assertDontSee($product->name, false)
            ->assertSee('name="search"', false)
            ->assertSee('value="beras"', false)
            ->assertSee('page=2', false)
            ->assertSee('search=beras', false);
    }

    public function test_empty_storefront_and_catalog_render_without_products(): void
    {
        $this->view('shop.index', ['products' => new Collection])
            ->assertSee(route('shop.products'), false);

        $view = $this->view('shop.products', [
            'products' => new LengthAwarePaginator([], 0, 12),
            'search' => 'produk tidak ada',
        ])->assertSee('name="search"', false);

        $this->assertDoesNotMatchRegularExpression('/<form\b[^>]*\bclass="[^"]*\badd-to-cart-form\b/s', (string) $view);
    }

    public function test_cart_renders_quantities_totals_and_ajax_update_contracts(): void
    {
        $this->actingAs($this->customer())->withSession(['cart' => $this->cart()]);

        $response = $this->get(route('cart.index'));
        $response->assertOk()
            ->assertSee('Beras Uji 5 kg')
            ->assertSee('Gula Uji 1 kg')
            ->assertSee('65.000')
            ->assertSee('50.000')
            ->assertSee('15.000')
            ->assertSee('name="quantity"', false)
            ->assertSee('value="2"', false)
            ->assertSee('value="3"', false)
            ->assertSee(route('cart.update', 101), false)
            ->assertSee(route('cart.remove', 102), false)
            ->assertSee(route('checkout.index'), false);

        foreach (['cart-item', 'quantity-update-form', 'remove-form', 'remove-btn', 'subtotal-amount', 'cart-total', 'cart-subtotal', 'cart-item-count'] as $hook) {
            $response->assertSee($hook, false);
        }
    }

    public function test_empty_cart_has_no_order_controls_and_checkout_redirects(): void
    {
        $this->actingAs($this->customer())->withSession(['cart' => []]);

        $this->get(route('cart.index'))->assertOk()
            ->assertDontSee('quantity-update-form', false)
            ->assertDontSee('remove-form', false);

        $this->get(route('checkout.index'))->assertRedirect(route('shop.index'));
    }

    public function test_checkout_keeps_customer_address_and_order_form_without_placing_an_order(): void
    {
        $customer = $this->customer();
        $this->actingAs($customer)->withSession(['cart' => $this->cart()]);

        $this->get(route('checkout.index'))->assertOk()
            ->assertSee($customer->name)
            ->assertSee($customer->address)
            ->assertSee('65.000')
            ->assertSee('action="'.route('checkout.placeOrder').'"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('type="submit"', false);

        $customer->address = null;
        $this->get(route('checkout.index'))->assertRedirect(route('shop.index'));
    }

    public function test_pending_order_keeps_payment_button_and_existing_order_values(): void
    {
        $this->actingAs($this->customer());
        $order = $this->order('Menunggu Pembayaran');

        $this->view('orders.show', ['order' => $order])
            ->assertSee('Beras Uji 5 kg')
            ->assertSee('50.000')
            ->assertSee('id="pay-button"', false)
            ->assertSee('https://app.sandbox.midtrans.com/snap/snap.js', false)
            ->assertSee('render-test-snap-token', false);

        $order->snap_token = null;
        $this->view('orders.show', ['order' => $order])
            ->assertDontSee('id="pay-button"', false);
    }

    public function test_order_lists_keep_detail_completion_invoice_and_empty_states(): void
    {
        $this->actingAs($this->customer());
        $order = $this->order('Dikirim');

        $this->view('orders.index', ['orders' => new Collection([$order])])
            ->assertSee(route('user.order.show', $order), false)
            ->assertSee(route('user.order.complete', $order), false);

        $this->view('orders.show', ['order' => $order])
            ->assertSee($order->tracking_message)
            ->assertDontSee('id="pay-button"', false);

        $order->status = 'Selesai';
        $this->view('orders.history', ['orders' => new Collection([$order])])
            ->assertSee(route('user.invoice.download', $order), false)
            ->assertSee('50.000');

        foreach (['orders.index', 'orders.history'] as $view) {
            $this->view($view, ['orders' => new Collection])
                ->assertSee(route('shop.index'), false);
        }
    }

    public function test_legacy_order_url_is_authenticated_and_does_not_shadow_current_url(): void
    {
        $legacy = Route::getRoutes()->getByName('user.order.legacy');

        $this->assertSame(UserOrderController::class.'@show', $legacy->getActionName());
        $this->assertContains('auth', $legacy->gatherMiddleware());
        $this->assertSame('/pesanan/123', route('user.order.show', 123, false));
        $this->assertSame('/orders/123', route('user.order.legacy', 123, false));
    }

    private function customer(): User
    {
        return (new User)->forceFill([
            'id_user' => 999,
            'name' => 'Pelanggan Uji',
            'email' => 'pelanggan@example.test',
            'address' => 'Jalan Contoh 10, Jakarta',
        ]);
    }

    private function product(): Product
    {
        return (new Product)->forceFill([
            'id_product' => 101,
            'name' => 'Beras Uji 5 kg',
            'price' => 25000,
            'current_stock' => 12,
            'photo_path' => null,
        ]);
    }

    private function cart(): array
    {
        return [
            101 => ['name' => 'Beras Uji 5 kg', 'quantity' => 2, 'price' => 25000, 'photo_path' => null],
            102 => ['name' => 'Gula Uji 1 kg', 'quantity' => 3, 'price' => 5000, 'photo_path' => null],
        ];
    }

    private function order(string $status): Order
    {
        $order = (new Order)->forceFill([
            'id' => 123,
            'user_id' => 999,
            'status' => $status,
            'total_price' => 50000,
            'payment_status' => $status === 'Menunggu Pembayaran' ? 'pending' : 'paid',
            'payment_expires_at' => now()->addMinutes(30),
            'snap_token' => 'render-test-snap-token',
            'customer_name' => 'Pelanggan Uji',
            'customer_email' => 'pelanggan@example.test',
            'shipping_address' => 'Jalan Contoh 10, Jakarta',
            'tracking_message' => 'Pesanan sedang diantar ke alamat tujuan.',
            'created_at' => now()->subHour(),
            'updated_at' => now(),
        ]);
        $item = (new OrderItem)->forceFill(['quantity' => 2, 'price' => 25000]);
        $item->setRelation('product', $this->product());
        $order->setRelation('items', new Collection([$item]));
        $order->setRelation('user', $this->customer());

        return $order;
    }
}
