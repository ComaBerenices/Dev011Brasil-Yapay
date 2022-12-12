<?php

namespace Dev011Brasil\App\Models\Contracts;

interface Parser
{
    /**
     * Get the error with base on code error
     * @param Mixed The code error 
     * @return String A message explains the code in text
     */
    public function getCodeMessage($codeError = -1);

    /**
     * Get the body response parsed 
     * @param Mixed The body from response    
     * @return Array The body parsed
     */
    public function getBodyResponse($bodyResponse = []);
}
