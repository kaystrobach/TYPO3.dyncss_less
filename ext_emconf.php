<?php

########################################################################
# Extension Manager/Repository config file for ext "less".
#
# Auto generated 09-03-2012 10:37
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF['dyncss_less'] = array(
    'title' => 'DynCss - Less Parser',
    'description' => 'DynCss-Parser',
    'category' => 'fe',
    'version' => '0.7.9',
    'state' => 'stable',
    'clearcacheonload' => 0,
    'author' => 'Kay Strobach',
    'author_email' => 'kay.strobach@typo3.org',
    'author_company' => '',
    'constraints' => array(
        'depends' => array(
            'dyncss' => '*',
            'typo3'  => '12.4.0-14.9.99'
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
);
