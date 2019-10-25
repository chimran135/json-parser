<?php
Class JsonParser
{
    public $data;
    public $emails;
    
    public function __construct($jsonString=NULL)
    {
        $this->data = NULL;
        $this->emails = NULL;
        $this->jsonImport($jsonString);
    }
    
    /**
     * Validate JSON
     *
     * @return Boolean
     */
    private function isJosnValid($jsonString=NULL)
    {
        if (!empty($jsonString)) {
            json_decode($jsonString);
            return (json_last_error() === JSON_ERROR_NONE);
        }
        return false;
    }
    
    /**
     * Imports required information from JSON to variables $data and $emails
     */
    private function jsonImport($jsonString=NULL)
    {
        try {
            if($this->isJosnValid($jsonString)) {

                $dataObj = json_decode($jsonString);

                if(isset($dataObj->data)) {
                    $emails = [];
                    foreach ($dataObj->data as $item) {
                        // Add new filed "name" in original data
                        $item->name = $item->first_name . ' ' . $item->last_name;
                        $emails[] = $item->email;
                    }
                    // Set comma sepearated emails
                    $this->emails = implode(',', $emails);

                    // sorted by age descending
                    usort($dataObj->data, function($a, $b) {
                        $r = $a->age < $b->age;
                        return $r;
                    });
                    $this->data = json_encode($dataObj);
                } else {
                    $this->data = 'Provided JSON is not in specified format.';
                }

            } else {
                $this->data = 'Provided JSON is not valid.';
            }
        } catch(Exception $ex) {
            echo 'Exception: '. $ex->getMessage();
        }
    }
}

// Create an instance
$object = new JsonParser('{"data":[{"first_name":"jake","last_name":"bennett","age":31,"email":"jake@bennett.com","secret":"VXNlIHRoaXMgc2VjcmV0IHBocmFzZSBzb21ld2hlcmUgaW4geW91ciBjb2RlJ3MgY29tbWVudHM="},{"first_name":"jordon","last_name":"brill","age":85,"email": "jordon@brill.com","secret":"YWxidXF1ZXJxdWUuIHNub3JrZWwu"}]}');

// a comma-separated list of email addresses 
echo $object->emails;

/* the original data, sorted by age descending, with a new field on each record
 * called "name" which is the first and last name joined.
 */
echo $object->data;