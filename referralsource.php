<?php

require_once 'referralsource.civix.php';
// phpcs:disable
use CRM_Referralsource_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_postProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postProcess
 */
function referralsource_civicrm_postProcess($formName, &$form) {
  // Detect contribution page and contribution confirm page
  if ($formName === 'CRM_Contribute_Form_Contribution_Confirm') {
    // Get the source param by the entryURL in the controler array
    $controller = $form->getVar('controller');
    // Process to get the source param
    $params = explode('?', $controller->_entryURL);
    parse_str(end($params), $parseURL);

    foreach ($parseURL as $key => $value) {
      // Remove amp; since it was not remove using parse_str
      $newKey = str_replace('amp;', '', $key);

      if ($newKey === 'source') {
        // Get the newly created contribution
        $lastContribution = civicrm_api3('Contribution', 'get', [
          'sequential' => 1,
          'id' => $form->_contributionID,
          'return' => ['id', 'contribution_source'],
        ]);

        // Add the source param value to the current source
        $newSource = $lastContribution['values'][0]['contribution_source'];
        // Add the source param value to the current source, if we haven't already
        // done so for this form (when running with a payment processor, this hook
        // is fired twice, so we have to keep track of this ourselves.)
        if (!isset($form->_params['_com.joineryhq.referralsource_processed'])) {
          $newSource = $lastContribution['values'][0]['contribution_source'] . ' - ' . $value;
          $form->_params['_com.joineryhq.referralsource_processed'] = TRUE;
        }

        // Update the source
        $result = civicrm_api3('Contribution', 'create', [
          'id' => $form->_contributionID,
          'source' => $newSource,
        ]);

        break;
      }
    }
  }
}

/**
 * Implements hook_civicrm_buildForm().
 * There are too many different paths to get to the thank you page for event registrations, so better to just add source on thankyou
 */
function referralsource_civicrm_buildForm($formName, &$form) {
  if ($formName === 'CRM_Event_Form_Registration_ThankYou') {
    $controller = $form->getVar('controller');
    $params = explode('?', $controller->_entryURL);
    parse_str(end($params), $parseURL);

    foreach ($parseURL as $key => $value) {
      $newKey = str_replace('amp;', '', $key);

      if ($newKey === 'source') {
        // if payment, then we get _participantIDS but if no payment, we only get primary _participantId (even if additionals exist)
        $participantIds = $form->getVar('_participantIDS');
        // so get additional participants if not given
        if (!($participantIds)) {
          $primaryParticipantId = $form->getVar('_participantId');
          // The API filters out test participants and won't return them unless you specify IS NOT NULL
          $additionaParticipants = \Civi\Api4\Participant::get(FALSE)
            ->addWhere('registered_by_id', '=', $primaryParticipantId)
            ->addWhere('is_test', 'IS NOT NULL')
            ->execute()->column('id');
          $participantIds = array_merge([$form->getVar('_participantId')], $additionaParticipants);
        }

        $primaryParticipant = \Civi\Api4\Participant::get(FALSE)
          ->addSelect('source')
          ->addWhere('id', '=', $participantIds[0])
          ->execute()->first();

        \Civi\Api4\Participant::update(FALSE)
          ->addValue('source', $primaryParticipant['source'] . ' - ' . $value)
          ->addWhere('id', 'IN', $participantIds)
          ->execute();

        break;
      }
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function referralsource_civicrm_config(&$config) {
  _referralsource_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function referralsource_civicrm_install() {
  _referralsource_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function referralsource_civicrm_enable() {
  _referralsource_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function referralsource_civicrm_preProcess($formName, &$form) {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function referralsource_civicrm_navigationMenu(&$menu) {
//  _referralsource_civix_insert_navigation_menu($menu, 'Mailings', array(
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ));
//  _referralsource_civix_navigationMenu($menu);
//}
