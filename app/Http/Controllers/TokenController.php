<?php

namespace App\Http\Controllers;

use App\DataTables\TokenDataTable;
use App\Models\Token;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     return view('token.index');
    // }
    public function index(TokenDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title',['form' => __('message.token')] );
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = ('Token add') ? '<a href="'.route('token.create').'" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> '.__('message.add_form_title',['form' => __('message.token')]).'</a>' : '';
        return $dataTable->render('global.datatable', compact('pageTitle','button','auth_user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title',[ 'form' => __('message.token')]);
        
        return view('token.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $token = Token::create($request->all());

        return redirect()->route('token.index')->withSuccess(__('message.save_form', ['form' => __('message.token')]));
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
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.token')]);
        $data = Token::findOrFail($id);
        
        return view('token.form', compact('data', 'pageTitle', 'id'));
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
        $token = Token::findOrFail($id);

        // token data...
        $token->fill($request->all())->update();

        if(auth()->check()){
            return redirect()->route('token.index')->withSuccess(__('message.update_form',['form' => __('message.token')]));
        }
        return redirect()->back()->withSuccess(__('message.update_form',['form' => __('message.token') ] ));
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
        $token = Token::findOrFail($id);
        $status = 'errors';
        $message = __('message.not_found_entry', ['name' => __('message.token')]);

        if($token != '') {
            $token->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.token')]);
        }

        if(request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message ]);
        }

        return redirect()->back()->with($status,$message);
    }
}
