<?php

class HandleController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $handles = Auth::user()->handles()->get();
        $myHandles = array();
        foreach($handles as $handle){
            $myHandles[] = $handle->name;
        }

        return Response::json(
            array(
                'handles' => $myHandles
            )
        );
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$handle = new Handle;
        $success = false;

        if (Input::has('name')){
            // make a new handle with username
            $validator = Validator::make(
                array('name' => Input::get('name')),
                array('name' => 'max:100|unique:handles|Regex:/^[a-zA-Z0-9][a-zA-Z0-9_\-]*$/')
            );

            if($validator->passes()){
                $handle->name = Input::get('name');
                $handle->user_id = Auth::id();
                $handle->save();

                $hashids = new Hashids\Hashids('beach days');
                $handle->id_hash = $hashids->encrypt($handle->id);

                $handle->save();

                $success = true;

                return Response::json(
                    array(
                        'result' => 'success',
                        'handle' => $handle->name
                    )
                );

            } else {
                // error with valid
                return Response::json(
                    array(
                        'result' => 'fail',
                        'message' => 'Failed validation'
                    )
                );
            }

        } elseif (Input::has('key')){
            // make a new key based handle

            $hashids = new Hashids\Hashids('beach days');

            $handle->user_id = Auth::id();
            $handle->save();

            $handle->id_hash = $hashids->encrypt($handle->id);
            $handle->name = $hashids->encrypt($handle->id);
            $handle->save();

            $success = true;

            return Response::json(
                array(
                    'result' => 'success',
                    'handle' => '_'.$handle->name
                )
            );


        } else {
            // not the right values passes
            return Response::json(
                array(
                    'result' => 'fail',
                    'message' => 'Incorrect values provided'
                )
            );
        }
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
