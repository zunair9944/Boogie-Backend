<?php

namespace App\Http\Controllers;

use App\DataTables\SubscriptionDataTable;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Mockery\Matcher\Subset;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SubscriptionDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title',['form' => __('message.subscription')] );
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = ('subscription add') ? '<a href="'.route('subscription.create').'" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> '.__('message.add_form_title',['form' => __('message.subscription')]).'</a>' : '';
        return $dataTable->render('global.datatable', compact('pageTitle','button','auth_user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title',[ 'form' => __('message.subscription')]);
        
        return view('subscription.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $subscription = Subscription::create($request->all());

        return redirect()->route('subscription.index')->withSuccess(__('message.save_form', ['form' => __('message.subscription')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.subscription')]);
        $data = Subscription::findOrFail($id);
        
        return view('subscription.form', compact('data', 'pageTitle', 'id'));
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
        $subscription = Subscription::findOrFail($id);

        // subscription data...
        $subscription->fill($request->all())->update();

        if(auth()->check()){
            return redirect()->route('subscription.index')->withSuccess(__('message.update_form',['form' => __('message.subscription')]));
        }
        return redirect()->back()->withSuccess(__('message.update_form',['form' => __('message.subscription') ] ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(env('APP_DEMO')){
            $message = __('message.demo_permission_denied');
            if(request()->ajax()) {
                return response()->json(['status' => true, 'message' => $message ]);
            }
            return redirect()->route('additionalfees.index')->withErrors($message);
        }
        $subscription = Subscription::findOrFail($id);
        $status = 'errors';
        $message = __('message.not_found_entry', ['name' => __('message.subscription')]);

        if($subscription != '') {
            $subscription->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.subscription')]);
        }

        if(request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message ]);
        }

        return redirect()->back()->with($status,$message);
    }
}
