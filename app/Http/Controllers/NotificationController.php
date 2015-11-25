<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = $this->getAuthUserId();

        $device_token = $request->device_token;

        if( !$device_token )
        {
            return $this->respondValidationFailed('Device token missing.');
        }
        $ios_device = false;

        if($request->device_type === 'ios')
        {
            $ios_device = true;
        }
        Notification::create(array(
            'user_id' => $user_id,
            'ios' => $ios_device,
            'token' => $device_token
        ));

        return $this->respondCreated('Device token accepted.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

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
