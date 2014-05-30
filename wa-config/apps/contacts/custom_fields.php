<?php
return array (
  0 => 
  waContactAddressField::__set_state(array(
     'id' => 'address',
     'options' => 
    array (
      'multi' => true,
      'ext' => 
      array (
        'work' => 'Work',
        'home' => 'Home',
        'shipping' => 'Shipping',
        'billing' => 'Billing',
      ),
      'storage' => 'data',
      'fields' => 
      array (
        'street' => 
        waContactStringField::__set_state(array(
           'id' => 'street',
           'options' => 
          array (
            'storage' => 'data',
            'validators' => 
            waStringValidator::__set_state(array(
               'messages' => 
              array (
                'required' => 'Нужно заполнить',
                'invalid' => 'Неверно',
                'max_length' => 'Пожалуйста, не более 0 символов',
                'min_length' => 'Пожалуйста, не менее 0 символов',
              ),
               'options' => 
              array (
                'required' => false,
                'storage' => 'data',
              ),
               'errors' => 
              array (
              ),
               '_type' => 'waStringValidator',
            )),
          ),
           'name' => 
          array (
            'en_US' => 'Street address',
          ),
           '_type' => 'waContactStringField',
        )),
        'city' => 
        waContactStringField::__set_state(array(
           'id' => 'city',
           'options' => 
          array (
            'storage' => 'data',
            'validators' => 
            waStringValidator::__set_state(array(
               'messages' => 
              array (
                'required' => 'Нужно заполнить',
                'invalid' => 'Неверно',
                'max_length' => 'Пожалуйста, не более 0 символов',
                'min_length' => 'Пожалуйста, не менее 0 символов',
              ),
               'options' => 
              array (
                'required' => false,
                'storage' => 'data',
              ),
               'errors' => 
              array (
              ),
               '_type' => 'waStringValidator',
            )),
          ),
           'name' => 
          array (
            'en_US' => 'City',
          ),
           '_type' => 'waContactStringField',
        )),
        'region' => 
        waContactRegionField::__set_state(array(
           'rm' => NULL,
           'id' => 'region',
           'options' => 
          array (
            'storage' => 'data',
          ),
           'name' => 
          array (
            'en_US' => 'State',
          ),
           '_type' => 'waContactRegionField',
        )),
        'zip' => 
        waContactStringField::__set_state(array(
           'id' => 'zip',
           'options' => 
          array (
            'storage' => 'data',
            'validators' => 
            waStringValidator::__set_state(array(
               'messages' => 
              array (
                'required' => 'Нужно заполнить',
                'invalid' => 'Неверно',
                'max_length' => 'Пожалуйста, не более 0 символов',
                'min_length' => 'Пожалуйста, не менее 0 символов',
              ),
               'options' => 
              array (
                'required' => false,
                'storage' => 'data',
              ),
               'errors' => 
              array (
              ),
               '_type' => 'waStringValidator',
            )),
          ),
           'name' => 
          array (
            'en_US' => 'ZIP',
          ),
           '_type' => 'waContactStringField',
        )),
        'country' => 
        waContactCountryField::__set_state(array(
           'model' => NULL,
           'id' => 'country',
           'options' => 
          array (
            'defaultOption' => 'Select country',
            'storage' => 'data',
            'formats' => 
            array (
              'value' => 
              waContactCountryFormatter::__set_state(array(
                 '_type' => 'waContactCountryFormatter',
                 'options' => NULL,
              )),
            ),
          ),
           'name' => 
          array (
            'en_US' => 'Country',
          ),
           '_type' => 'waContactCountryField',
        )),
        'perevozchik3' => 
        waContactSelectField::__set_state(array(
           'id' => 'perevozchik3',
           'options' => 
          array (
            'app_id' => 'shop',
            'storage' => 'data',
            'options' => 
            array (
              'Новая почта' => 'Новая почта',
              'Ночной экспресс' => 'Ночной экспресс',
              'Автолюкс' => 'Автолюкс',
              'Самовывоз' => 'Самовывоз',
            ),
            'required' => '',
          ),
           'name' => 
          array (
            'en_US' => 'Перевозчик',
          ),
           '_type' => 'waContactSelectField',
        )),
        'sklad-perevozch' => 
        waContactStringField::__set_state(array(
           'id' => 'sklad-perevozch',
           'options' => 
          array (
            'app_id' => 'shop',
            'storage' => 'data',
            'validators' => 
            waStringValidator::__set_state(array(
               'messages' => 
              array (
                'required' => 'Нужно заполнить',
                'invalid' => 'Неверно',
                'max_length' => 'Пожалуйста, не более 0 символов',
                'min_length' => 'Пожалуйста, не менее 0 символов',
              ),
               'options' => 
              array (
                'required' => false,
                'app_id' => 'shop',
                'storage' => 'data',
              ),
               'errors' => 
              array (
              ),
               '_type' => 'waStringValidator',
            )),
            'required' => '',
          ),
           'name' => 
          array (
            'en_US' => 'Склад перевозчика',
          ),
           '_type' => 'waContactStringField',
        )),
        'primechanie' => 
        waContactStringField::__set_state(array(
           'id' => 'primechanie',
           'options' => 
          array (
            'app_id' => 'shop',
            'storage' => 'data',
            'validators' => 
            waStringValidator::__set_state(array(
               'messages' => 
              array (
                'required' => 'Нужно заполнить',
                'invalid' => 'Неверно',
                'max_length' => 'Пожалуйста, не более 0 символов',
                'min_length' => 'Пожалуйста, не менее 0 символов',
              ),
               'options' => 
              array (
                'required' => false,
                'app_id' => 'shop',
                'storage' => 'data',
              ),
               'errors' => 
              array (
              ),
               '_type' => 'waStringValidator',
            )),
            'required' => '',
          ),
           'name' => 
          array (
            'en_US' => 'Примечание',
          ),
           '_type' => 'waContactStringField',
        )),
      ),
      'formats' => 
      array (
        'js' => 
        waContactAddressOneLineFormatter::__set_state(array(
           '_type' => 'waContactAddressOneLineFormatter',
           'options' => NULL,
        )),
      ),
      'required' => 
      array (
      ),
      'unique' => false,
    ),
     'name' => 
    array (
      'en_US' => 'Address',
    ),
     '_type' => 'waContactAddressField',
  )),
  1 => 
  waContactStringField::__set_state(array(
     'id' => 'organizatsiya',
     'options' => 
    array (
      'app_id' => 'shop',
      'storage' => 'data',
      'validators' => 
      waStringValidator::__set_state(array(
         'messages' => 
        array (
          'required' => 'Нужно заполнить',
          'invalid' => 'Неверно',
          'max_length' => 'Пожалуйста, не более 0 символов',
          'min_length' => 'Пожалуйста, не менее 0 символов',
        ),
         'options' => 
        array (
          'required' => false,
          'app_id' => 'shop',
          'storage' => 'data',
        ),
         'errors' => 
        array (
        ),
         '_type' => 'waStringValidator',
      )),
      'required' => false,
      'unique' => false,
    ),
     'name' => 
    array (
      'en_US' => 'Организация',
    ),
     '_type' => 'waContactStringField',
  )),
);
// EOF