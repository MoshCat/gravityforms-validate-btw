<?php

// https://docs.gravityforms.com/gform_validation 
// https://controleerbtwnummer.eu/api
add_filter('gform_validation_2', function ($validation_result) {
    $formID   = '2';
    $fieldID  = '11';
    $form     = $validation_result['form'];
    $entry    = GFFormsModel::get_current_lead();
    $validate = @file_get_contents('https://controleerbtwnummer.eu/api/validate/'. rgar($entry, $fieldID) .'.json');

    if ($validate === false) {
        throw new Exception('service unavailable');
    } else {
        $res = json_decode($validate);
        if ($res->valid) {
            // vat number is valid
            // echo 'Valid!';
        } else {
            $validation_result['is_valid'] = false;
            // finding Field with ID of 1 and marking it as failed validation
            foreach ($form['fields'] as &$field) {
                //NOTE: replace 1 with the field you would like to validate
                if ($field->id == $fieldID) {
                    $field->failed_validation  = true;
                    $field->validation_message = 'This field is invalid!';
                    break;
                }
            }
        }
        // var_dump($res);
    }
 
    //Assign modified $form object back to the validation result
    $validation_result['form'] = $form;
 
    return $validation_result;
});
