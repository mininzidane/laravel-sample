<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\User;
use App\Service\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function homepage(Request $request, TransactionService $transactionService): View
    {
        /**
         * @var User $user
         * @var Partner $partner
         */
        if ($request->get('user_id')) {
            $user = User::find($request->get('user_id')) ?? User::first();
        } else {
            $user = User::first();
        }

        if ($request->get('partner_id')) {
            $partner = Partner::find($request->get('partner_id')) ?? Partner::first();
        } else {
            $partner = Partner::first();
        }

        $errors = [];
        if ($request->isMethod('post')) {
            if ($request->get('user_balance_send')) {
                try {
                    $request->validate([
                        'user_balance_value' => 'integer|required',
                    ]);

                    $result = $transactionService->withdrawAmountFromUser((int) $request->get('user_balance_value'), $user, $partner);
                    if ($result !== true) {
                        $errors[] = $transactionService->getErrorLabel($result);
                    }
                } catch (ValidationException $e) {
                    $errors[] = $e->getMessage();
                }
            }

            if ($request->get('partner_balance_send')) {
                $result = $transactionService->withdrawFromPartner($partner);
                if ($result !== true) {
                    $errors[] = $transactionService->getErrorLabel($result);
                }
            }

            if ($request->get('user_cashback_send')) {
                $result = $transactionService->transferCashbackToUser($user);
                if ($result !== true) {
                    $errors[] = $transactionService->getErrorLabel($result);
                }
            }
        }

        return view('homepage', [
            'user' => $user,
            'partner' => $partner,
            'errors' => $errors,
        ]);
    }
}
