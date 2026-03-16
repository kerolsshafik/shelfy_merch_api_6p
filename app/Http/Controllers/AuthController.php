<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function remove_user(Request $request)
    {
        return view('Account.Remove_user');
    }


    public function success(Request $request)
    {
        return view('Account.SuccessRemove');
    }

    public function destroy(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'email' => 'required|exists:customers,email',
            'password' => 'required|string|min:8',
        ]);
        $customer = Customer::where('email', $request->email)->first();
        if (!$customer) {
            return redirect()->back()->withErrors(['email' => 'The provided email does not match our records.']);
        }
        if (!Hash::check($request->password, $customer->password)) {
            return redirect()->back()->withErrors(['password' => 'The provided password does not match our records.']);
        }

        $customer->delete();

        return redirect()->route('Success')->with('message', 'User deleted successfully.');
    }



}
