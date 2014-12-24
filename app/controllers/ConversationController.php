<?php

class ConversationController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $user = Auth::user();
        $handles = $user->handles()->get();

        $conversationArr = array();
        foreach($handles as $handle){
            foreach($handle->conversations()->get() as $conversation){
                $handleArr = array();
                foreach($conversation->handles()->get() as $handle){
                    if(isset($handle->name)){
                        $handleArr[] = $handle->name;
                    } else {
                        $handleArr[] = $handle->id_hash;
                    }
                }

                $conversationArr[] = array(
                    'id' => $conversation->id_hash,
                    'name' => $conversation->name,
                    'is_encrypted' => $conversation->is_encrypted,
                    'last_active_ts' => $conversation->updated_at->getTimestamp(),
                    'handles' => $handleArr
                );
            }
        }
        return Response::json(
            array(
                'result' => 'success',
                'conversations' => $conversationArr
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
        $user = Auth::user();
        if (Input::has('my_handle') && Input::has('guest_handle')){
            foreach($user->handles()->get() as $myHandle){
                if($myHandle->name == Input::get('my_handle') || '_'.$myHandle->id_hash == Input::get('my_handle')){
                    $guestHandle = Input::get('guest_handle');

                    if(strpos($guestHandle, '_') === 0){
                        // this is a key based handle
                        $guest = Handle::where('id_hash', '=', substr($guestHandle, 1))->first();
                    } else {
                        // get handle by name
                        $guest = Handle::where('name', '=', $guestHandle)->first();
                    }

                    if(count($guest)){
                        // guest is valid, create conversation
                        $conversation = new Conversation;
                        $conversation->user_id = $user->id;
                        $conversation->is_encrypted = false;
                        $conversation->last_active_ts = new \DateTime;
                        $conversation->save();

                        $hashIds = new Hashids\Hashids('summer nights');
                        $conversation->id_hash = $hashIds->encrypt($conversation->id);
                        $conversation->save();

                        $conversation->handles()->sync(array($guest->id, $user->id));

                        return Response::json(
                            array(
                                'result' => 'success',
                                'conversation' => $conversation->id_hash
                            )
                        );

                    } else {
                        // couldn't find guest
                        return Response::json(
                            array(
                                'result' => 'fail',
                                'message' => 'Guest username invalid'
                            )
                        );
                    }
                }
            }
        }
	}
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function showAfter($id, $ts)
    {
        if(!is_numeric($ts)){
            // todo: return with error
            return false;
        }
        $conversation = Conversation::where('id_hash', '=', $id)->first();
        $user = Auth::user();
        $myHandleArr = array();

        foreach($user->handles()->get() as $myHandle){
            $myHandleArr[] = $myHandle->id;
        }

        if(count($conversation->handles()->whereIn('handles.id', $myHandleArr)->get())){
            // this user is in the conversation

            $handles = array();

            foreach($conversation->handles()->get() as $handle){
                if(isset($handle->name)){
                    $handles[$handle->id] = $handle->name;
                } else {
                    $handles[$handle->id] = '_'.$handle->id_hash;
                }
            }

            $date = new DateTime();
            $date->setTimestamp($ts);

            $messages = $conversation->messages()->where('created_at', '>', $date)->get();
            $newMessages = array();
            foreach($messages as $message){
                $newMessages[] = array(
                    'id' => $message->id,
                    'handle' => $handles[$message->handle_id],
                    'message' => $message->content,
                    'ts' => $message->created_at->getTimestamp()
                );
            }
            return Response::json(
                array(
                    'result' => 'success',
                    'messages' => $newMessages
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
        $conversation = Conversation::where('id_hash', '=', $id)->first();
        $user = Auth::user();
        $myHandleArr = array();

        foreach($user->handles()->get() as $myHandle){
            $myHandleArr[] = $myHandle->id;
        }

        if(count($conversation->handles()->whereIn('handles.id', $myHandleArr)->get())){
            // this user is in the conversation

            $handles = array();

            foreach($conversation->handles()->get() as $handle){
                if(isset($handle->name)){
                    $handles[$handle->id] = $handle->name;
                } else {
                    $handles[$handle->id] = '_'.$handle->id_hash;
                }
            }

            $messages = $conversation->messages()->take(50)->orderBy('created_at', 'desc')->get();
            $newMessages = array();
            foreach($messages as $message){
                $newMessages[] = array(
                    'id' => $message->id,
                    'handle' => $handles[$message->handle_id],
                    'message' => $message->content,
                    'ts' => $message->created_at->getTimestamp()
                );
            }
            return Response::json(
                array(
                    'result' => 'success',
                    'messages' => array_reverse($newMessages)
                )
            );
        }
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
