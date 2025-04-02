<?php

namespace Tests\Http\Controllers\API;

use App\Http\Controllers\API\TransactionController;
use App\Http\Requests\Transaction\DepositRequest;
use App\Http\Requests\Transaction\TransferRequest;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_successful()
    {
        $this->mockAuthUser(1);

        $transferRequestData = [
            'receiver_id' => 2,
            'amount' => 100.00,
            'description' => 'Test Transfer',
        ];

        $mockService = Mockery::mock(TransactionService::class, function (MockInterface $mock) use ($transferRequestData) {
            $mock->shouldReceive('transfer')
                ->once()
                ->with(Mockery::type(\App\DTOs\Transaction\TransferDTO::class))
                ->andReturn([
                    'success' => true,
                    'transaction' => new \App\Models\Transaction(),
                    'message' => 'Transfer successful'
                ]);
        });

        $this->app->instance(TransactionService::class, $mockService);

        $controller = $this->app->make(TransactionController::class);

        $this->instance(TransferRequest::class, $this->createTransferRequest($transferRequestData));

        $response = $controller->transfer(app(TransferRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertEquals('Transfer successful', $responseData['message']);
    }

    public function test_transfer_fails()
    {
        $this->mockAuthUser(1);

        $transferRequestData = [
            'receiver_id' => 2,
            'amount' => 100.00,
            'description' => 'Test Transfer',
        ];

        $mockService = Mockery::mock(TransactionService::class, function (MockInterface $mock) {
            $mock->shouldReceive('transfer')
                ->once()
                ->with(Mockery::type(\App\DTOs\Transaction\TransferDTO::class))
                ->andReturn([
                    'success' => false,
                    'message' => 'Insufficient balance'
                ]);
        });

        $this->app->instance(TransactionService::class, $mockService);

        $controller = $this->app->make(TransactionController::class);

        $this->instance(TransferRequest::class, $this->createTransferRequest($transferRequestData));

        $response = $controller->transfer(app(TransferRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);
        $this->assertEquals(false, $responseData['success']);
        $this->assertEquals('Insufficient balance', $responseData['message']);
    }

    private function createTransferRequest(array $data): TransferRequest
    {
        $request = TransferRequest::create('/', 'POST', $data);
        $request->setUserResolver(function () use ($data) {
            return $data['sender_id'] ?? null;
        });
        return $request;
    }

    private function mockAuthUser(int $userId): void
    {
        $this->be(Mockery::mock(\App\Models\User::class, function (MockInterface $mock) use ($userId) {
            $mock->shouldReceive('getAuthIdentifier')
                ->andReturn($userId);
            $mock->shouldReceive('getAuthIdentifierName')
                ->andReturn('id');
        }));
    }

    public function test_deposit_successful()
    {
        $this->mockAuthUser(1);

        $depositRequestData = [
            'amount' => 100.00,
            'description' => 'Test Deposit',
        ];

        $mockService = Mockery::mock(TransactionService::class, function (MockInterface $mock) use ($depositRequestData) {
            $mock->shouldReceive('deposit')
                ->once()
                ->with(Mockery::type(\App\DTOs\Transaction\DepositDTO::class))
                ->andReturn([
                    'success' => true,
                    'transaction' => new \App\Models\Transaction(),
                    'message' => 'Deposit successful'
                ]);
        });

        $this->app->instance(TransactionService::class, $mockService);

        $controller = $this->app->make(TransactionController::class);

        $this->instance(DepositRequest::class, $this->createDepositRequest($depositRequestData));

        $response = $controller->deposit(app(DepositRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertEquals('Deposit successful', $responseData['message']);
    }

    public function test_deposit_fails()
    {
        $this->mockAuthUser(1);

        $depositRequestData = [
            'amount' => 100.00,
            'description' => 'Test Deposit',
        ];

        $mockService = Mockery::mock(TransactionService::class, function (MockInterface $mock) {
            $mock->shouldReceive('deposit')
                ->once()
                ->with(Mockery::type(\App\DTOs\Transaction\DepositDTO::class))
                ->andReturn([
                    'success' => false,
                    'message' => 'Failed to deposit'
                ]);
        });

        $this->app->instance(TransactionService::class, $mockService);

        $controller = $this->app->make(TransactionController::class);

        $this->instance(DepositRequest::class, $this->createDepositRequest($depositRequestData));

        $response = $controller->deposit(app(DepositRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);
        $this->assertEquals(false, $responseData['success']);
        $this->assertEquals('Failed to deposit', $responseData['message']);
    }

    private function createDepositRequest(array $data): DepositRequest
    {
        $request = DepositRequest::create('/', 'POST', $data);
        $request->setUserResolver(function () use ($data) {
            return $data['user_id'] ?? null;
        });
        return $request;
    }

    public function test_reverse_transaction_successful()
    {
        $this->mockAuthUser(1);

        $reversalRequestData = [
            'transaction_id' => 1,
            'description' => 'Reversing test transaction',
        ];

        $mockService = Mockery::mock(TransactionService::class, function (MockInterface $mock) use ($reversalRequestData) {
            $mock->shouldReceive('reverse')
                ->once()
                ->with(Mockery::type(\App\DTOs\Transaction\ReversalDTO::class))
                ->andReturn([
                    'success' => true,
                    'transaction' => new \App\Models\Transaction(),
                    'message' => 'Transaction reversed successfully'
                ]);
        });

        $this->app->instance(TransactionService::class, $mockService);

        $controller = $this->app->make(TransactionController::class);

        $this->instance(\App\Http\Requests\Transaction\ReversalRequest::class, $this->createReversalRequest($reversalRequestData));

        $response = $controller->reverse(app(\App\Http\Requests\Transaction\ReversalRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertEquals('Transaction reversed successfully', $responseData['message']);
    }

    public function test_reverse_transaction_fails()
    {
        $this->mockAuthUser(1);

        $reversalRequestData = [
            'transaction_id' => 1,
            'description' => 'Reversing test transaction',
        ];

        $mockService = Mockery::mock(TransactionService::class, function (MockInterface $mock) {
            $mock->shouldReceive('reverse')
                ->once()
                ->with(Mockery::type(\App\DTOs\Transaction\ReversalDTO::class))
                ->andReturn([
                    'success' => false,
                    'message' => 'Failed to reverse transaction'
                ]);
        });

        $this->app->instance(TransactionService::class, $mockService);

        $controller = $this->app->make(TransactionController::class);

        $this->instance(\App\Http\Requests\Transaction\ReversalRequest::class, $this->createReversalRequest($reversalRequestData));

        $response = $controller->reverse(app(\App\Http\Requests\Transaction\ReversalRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);
        $this->assertEquals(false, $responseData['success']);
        $this->assertEquals('Failed to reverse transaction', $responseData['message']);
    }

    private function createReversalRequest(array $data): \App\Http\Requests\Transaction\ReversalRequest
    {
        $request = \App\Http\Requests\Transaction\ReversalRequest::create('/', 'POST', $data);
        $request->setUserResolver(fn() => $data['user_id'] ?? null);
        return $request;
    }

    public function test_history_success()
    {
        $this->mockAuthUser(1);

        // Crie uma data para o created_at
        $createdAt = \Carbon\Carbon::now();

        $mockTransactions = collect([
            Mockery::mock(\App\Models\Transaction::class, function (MockInterface $mock) use ($createdAt) {
                $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
                $mock->shouldReceive('getAttribute')->with('reference_id')->andReturn('REF123');
                $mock->shouldReceive('getAttribute')->with('type')->andReturn('payment');
                $mock->shouldReceive('getAttribute')->with('sender_id')->andReturn(1);
                $mock->shouldReceive('getAttribute')->with('amount')->andReturn(100.00);
                $mock->shouldReceive('getAttribute')->with('status')->andReturn('completed');
                $mock->shouldReceive('getAttribute')->with('description')->andReturn('Successful transaction');
                $mock->shouldReceive('getAttribute')->with('created_at')->andReturn($createdAt);
                $mock->shouldReceive('getAttribute')->with('receiver')->andReturn(null);
                $mock->shouldReceive('getAttribute')->with('sender')->andReturn(null);
            }),
            Mockery::mock(\App\Models\Transaction::class, function (MockInterface $mock) use ($createdAt) {
                $mock->shouldReceive('getAttribute')->with('id')->andReturn(2);
                $mock->shouldReceive('getAttribute')->with('reference_id')->andReturn('REF456');
                $mock->shouldReceive('getAttribute')->with('type')->andReturn('payment');
                $mock->shouldReceive('getAttribute')->with('sender_id')->andReturn(1);
                $mock->shouldReceive('getAttribute')->with('amount')->andReturn(200.00);
                $mock->shouldReceive('getAttribute')->with('status')->andReturn('completed');
                $mock->shouldReceive('getAttribute')->with('description')->andReturn('Another transaction');
                $mock->shouldReceive('getAttribute')->with('created_at')->andReturn($createdAt);
                $mock->shouldReceive('getAttribute')->with('receiver')->andReturn(null);
                $mock->shouldReceive('getAttribute')->with('sender')->andReturn(null);
            }),
        ]);

        $mockService = Mockery::mock(TransactionService::class, function (MockInterface $mock) use ($mockTransactions) {
            $mock->shouldReceive('getUserTransactions')
                ->once()
                ->with(1)
                ->andReturn($mockTransactions);
        });

        $this->app->instance(TransactionService::class, $mockService);

        $controller = $this->app->make(TransactionController::class);

        $response = $controller->history();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertCount(2, $responseData['data']['transactions']);
    }


    public function test_history_no_transactions()
    {
        $this->mockAuthUser(1);

        $mockService = Mockery::mock(TransactionService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getUserTransactions')
                ->once()
                ->with(1)
                ->andReturn(collect([]));
        });

        $this->app->instance(TransactionService::class, $mockService);

        $controller = $this->app->make(TransactionController::class);

        $response = $controller->history();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertEmpty($responseData['data']['transactions']);
    }
}
