<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Str;

class ProfileController extends Controller
{
    public function edit()
    {
        $user=User::findOrFail(auth()->user()->id);

        return Inertia::render('Profile/Edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request, User $user)
    {
        $validatedData = $request->validated();
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->old_password,
        ])) {
            throw ValidationException::withMessages([
                'old_password' => __('auth.password'),
            ]);
        }

        $user->update([
            'name' =>  $validatedData['name'],
            'email' => $validatedData['email'],
            'password' =>  $validatedData['new_password'] ? Hash::make($request->new_password) : Hash::make($request->old_password),
        ]);
        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
