<?php namespace Champ\Listeners;

use Laracasts\Commander\Events\EventListener;
use Champ\Join\Events\JoinStatusChanged;
use Mail;

class NotificationListener extends EventListener {

    protected $notifiable = [
        3, // Paid
        7 // Canceled
    ];

    /**
     * Update the championship limit when a user join the championship
     *
     * @param  JoinStatusChanged $event
     * @return void
     */
    public function whenJoinStatusChanged(JoinStatusChanged $event)
    {
        $join = $event->join;

        $parameters = [
            'name' => $join->user->name,
            'championship' => $join->championship->name
        ];

        if (in_array($join->status_id, $this->notifiable))
        {
            Mail::send('emails.status_changed', $parameters, function($message)
            {
                $message->to($join->user->email)->subject("O Status da sua inscrição mudou");
            });
        }
    }

}