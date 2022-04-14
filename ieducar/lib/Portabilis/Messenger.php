<?php

class Portabilis_Messenger
{
    public function __construct()
    {
        $this->_msgs = [];
    }

    public function append($msg, $type = 'error', $encodeToUtf8 = false, $ignoreIfHasMsgWithType = '')
    {
        if (empty($ignoreIfHasMsgWithType) || !$this->hasMsgWithType($ignoreIfHasMsgWithType)) {
            if ($encodeToUtf8) {
                $msg = utf8_encode($msg);
            }

            $this->_msgs[] = ['msg' => $msg, 'type' => $type];
        }
    }

    public function hasMsgWithType($type)
    {
        $hasMsg = false;

        foreach ($this->_msgs as $m) {
            if ($m['type'] == $type) {
                $hasMsg = true;
                break;
            }
        }

        return $hasMsg;
    }

    public function toHtml($tag = 'p')
    {
        $msgs = '';

        foreach ($this->getMsgs() as $m) {
            $msgs .= "<$tag class='{$m['type']}'>{$m['msg']}</$tag>";
        }

        return $msgs;
    }

    public function getMsgs()
    {
        $msgs = [];

        // expoe explicitamente apenas as chaves 'msg' e 'type', evitando
        // exposição indesejada de chaves adicionadas futuramente ao array
        // $this->_msgs

        foreach ($this->_msgs as $m) {
            $msgs[] = ['msg' => $m['msg'], 'type' => $m['type']];
        }

        return $msgs;
    }

    public function merge($anotherMessenger)
    {
        foreach ($anotherMessenger->getMsgs() as $msg) {
            $this->append($msg['msg'], $msg['type']);
        }
    }
}
