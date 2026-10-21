<?php
/*
http://hu1.php.net/imap
http://davidwalsh.name/gmail-php-imap
http://www.sitepoint.com/exploring-phps-imap-library-1/
*/

class Imapclient {

    private $_mPath;

    private $_mUser;

    private $_mPass;

    private $_mMailbox = 'INBOX';

    private $_imap;

    public function __construct($path, $user, $pass, $mailbox = NULL) {

        $this->_mPath = "{{$path}}";

        $this->_mUser = $user;

        $this->_mPass = $pass;

        $this->_mMailbox = !empty($mailbox) ? $mailbox : 'INBOX';
    }

    private function _open_mailbox($mailboxName = NULL) {

        $mailbox = !empty($mailboxName) ? $mailboxName : $this->_mMailbox;

        try {
                
            $this->_imap = imap_open("{$this->_mPath}{$mailbox}", $this->_mUser, $this->_mPass);
        } catch (Exception $e) {
                
            exit($e->getMesssage());
        }

        return TRUE;
    }

    public function get_mailboxes() {

        $mailboxes = array ();

        if (empty($this->_imap)) {
                
            $this->_open_mailbox();
        }

        $dirlist = imap_list($this->_imap, $this->_mPath, '*');

        if (!empty($dirlist)) {
            foreach ($dirlist as $dir) {

                $mailboxes[] = str_replace($this->_mPath, '', imap_utf7_decode($dir));
            }
        }

        return $mailboxes;
    }

    public function get_mailbox_info() {

        $info = array ();

        if (empty($this->_imap)) {
                
            $this->_open_mailbox();
        }

        $data = imap_mailboxmsginfo($this->_imap);

        if (!empty($data)) {
                
            $pattern = '/' . "{(.+)}(.+)" . '/';
                
            preg_match($pattern, $data->Mailbox, $matches);
                
            $info['mailbox'] = !empty($matches[2]) ? $matches[2] : $data->Mailbox;
                
            $info['total'] = $data->Nmsgs;
                
            $info['unread'] = $data->Unread;
                
            $info['size'] = $data->Size;
                
            $info['date'] = date('Y-m-d H:i:s', strtotime($data->Date));
        }

        return $info;
    }

    public function get_message_count($mailboxName = NULL) {

        $mailbox = !empty($mailboxName) ? $mailboxName : $this->_mMailbox;

        $info = $this->get_mailbox_info();

        if (!empty($info)) {
                
            if ($info['mailbox'] != $mailbox) {

                $this->_open_mailbox($mailbox);
            }
                
            return imap_num_msg($this->_imap);
        }

        return FALSE;
    }

    public function get_message_list($mailboxName = NULL) {

        $messages = array();

        $mailbox = !empty($mailboxName) ? $mailboxName : $this->_mMailbox;

        if (empty($this->_imap)) {
                
            $this->_open_mailbox($mailbox);
        }

        $message_list = imap_sort($this->_imap, SORTARRIVAL, 1);

        if (!empty($message_list)) {
                
            foreach ($message_list as $id) {

                $header = imap_header($this->_imap, $id);

                if (!empty($header)) {
                    
                    
                        
                    $messages[] = array (
                            'id' => $id, //message number, can change
                            'uid' => imap_uid($this->_imap, $id), //unique only to the current folder
                            'message_id' => $header->message_id, //not necessarily present
                            'date' => date('Y-m-d H:i:s', strtotime($header->date)),
                            'to' => array (
                                    'name' => !empty($header->to[0]->personal) ? $header->to[0]->personal : NULL,
                                    'email' => "{$header->to[0]->mailbox}@{$header->to[0]->host}"
                            ),
                            'from' => array (
                                    'name' => !empty($header->from[0]->personal) ? $header->from[0]->personal : NULL,
                                    'email' => "{$header->from[0]->mailbox}@{$header->from[0]->host}"
                            ),
                            'subject' => $header->subject,
                            'size' => $header->Size
                    );
                }
            }
        }

        return $messages;
    }
}
?>