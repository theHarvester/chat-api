<?php

class MessageController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
        if(Input::has('conversation_id') && Input::has('message')){
            $messageInput = trim(Input::get('message'));
            if(strlen($messageInput) > 3000){
                return Response::json(
                    array(
                        'result' => 'fail',
                        'message' => 'Message too large'
                    )
                );
            }

            $conversation = Conversation::where('id_hash', '=', Input::get('conversation_id'))->first();
            $user = Auth::user();
            $myHandleArr = array();

            foreach ($user->handles()->get() as $myHandle) {
                $myHandleArr[] = $myHandle->id;
            }

            $myConversationHandle = $conversation->handles()->whereIn('handles.id', $myHandleArr)->first();

            if (count($myConversationHandle)) {
                // this user is in the conversation
                $message = new Message();
                $message->handle_id = $myConversationHandle->pivot->handle_id;
                $message->conversation_id = $conversation->id;
                $message->content = $messageInput;
                $message->burn_after_open = false;

                $date = new \DateTime();
                $date->add(new \DateInterval('P28D'));
                $message->burn_ts = $date;
                $message->save();

                $hashIds = new Hashids\Hashids('sunny weather');
                $message->id_hash = $hashIds->encrypt($message->id);
                $message->save();

                return Response::json(
                    array(
                        'result' => 'success',
                        'message' => 'Message sent',
                        'message_id' => $message->id_hash,
                        'timestamp' => $message->created_at
                    )
                );
            } else {
                // you're not in this conversation buddy
                return Response::json(
                    array(
                        'result' => 'fail',
                        'message' => 'Handle not permitted in this conversation'
                    )
                );
            }
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
