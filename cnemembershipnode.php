<?php

require_once 'cnemembershipnode.civix.php';
use CRM_Cnemembershipnode_ExtensionUtil as E;

function cnemembershipnode_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Membership' && $op == 'create') {
    if ($objectRef->membership_type_id == 2 && $objectRef->status_id == 1) {
      try {
        $contact = civicrm_api3('Contact', 'getsingle', [
          'sequential' => 1,
          'id' => $objectRef->contact_id,
        ]);
        $website = civicrm_api3('Website', 'get', [
          'sequential' => 1,
          'contact_id' => $objectRef->contact_id,
        ]);
      }
      catch (CiviCRM_API3_Exception $e) {
        $error = $e->getMessage();
        CRM_Core_Error::debug_log_message(ts('API Error %1', array(
          'domain' => 'com.aghstrategies.rjdformsubmission',
          1 => $error,
        )));
      }
    }
    if ($contact) {
      //Drupal specific code
      global $user;
      $node = new stdClass();
      $node->title = $contact['display_name'];
      $node->type = 'nonprofit';
      // Sets some defaults. Invokes hook_prepare() and hook_node_prepare().
      node_object_prepare($node);
      // Or e.g. 'en' if locale is enabled.
      $node->language = LANGUAGE_NONE;
      $node->uid = $user->uid;
      // Status is 1 or 0; published or not.
      $node->status = 0;
      // Promote is 1 or 0; promoted to front page or not.
      $node->promote = 0;
      // Comment is 0, 1, 2; 0 = disabled, 1 = read only, or 2 = read/write.
      $node->comment = 0;
      //TEXT FIELDS
      $node->field_url[$node->language][]['value'] = $website['values'][0]['url'];
      $node->field_address[$node->language][]['value'] = $contact['street_address'];
      $node->field_address2[$node->language][]['value'] = $contact['supplemental_address_1'];
      $node->field_zip[$node->language][]['value'] = $contact['postal_code'];
      $node->field_phone_ce[$node->language][]['value'] = $contact['phone'];
      $node->field_ci_email[$node->language][]['value'] = $contact['email'];
      $node->field_primary_contact_last[$node->language][]['value'] = "";
      $node->field_primary_contact_email[$node->language][]['value'] = "";
      $node->field_ni_mission[$node->language][]['value'] = $contact['custom_2'];
      $node->field_ni_programs[$node->language][]['value'] = $contact['custom_3'];
      $node->field_ni_yearfounded[$node->language][]['value'] = $contact['custom_38'];
      $node->field_ni_director[$node->language][]['value'] = $contact['custom_16'];
      $node->field_ein[$node->language][]['value'] = $contact['custom_87'];

      //SELECT FIELDS
      $node->field_mdd_city[$node->language][]['value'] = $contact['city'];
      $node->field_state[$node->language][]['value'] = $contact['state_province'];
      $node->field_gi_budget[$node->language][]['value'] = $contact['custom_101'];
      $node->field_ni_personnel[$node->language][]['value'] = $contact['custom_103'];
      $node->field_nplegal[$node->language][]['value'] = $contact['custom_119'];

      //CHECKBOXES
      $node->field_county[$node->language][]['value'] = $contact['custom_98'];
      $node->field_ni_ntee[$node->language][]['value'] = $contact['custom_95'];
      $node->field_ni_pop[$node->language][]['value'] = $contact['custom_96'];
      $node->field_ni_counties[$node->language][]['value'] = $contact['custom_97'];
      $node->field_volunteer[$node->language][]['value'] = $contact['custom_100'];
      $node->field_types_services_offered[$node->language][]['value'] = $contact['custom_90'];

      //LINK FIELDS
      $node->field_facebook[$node->language][]['url'] = $contact['custom_72'];
      $node->field_twitter[$node->language][]['url'] = $contact['custom_75'];
      $node->field_linkedin[$node->language][]['url'] = $contact['custom_71'];
      $node->field_youtube[$node->language][]['url'] = $contact['custom_91'];

      // Entity reference field.
      $node->field_civicrm_org[$node->language][] = array(
        'target_id' => $contact['id'],
        // "taxonomy_term" or other valid entity machine name.
        'target_type' => 'civicrm_contact',
      );

      $node = node_submit($node);
      node_save($node);
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function cnemembershipnode_civicrm_config(&$config) {
  _cnemembershipnode_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function cnemembershipnode_civicrm_xmlMenu(&$files) {
  _cnemembershipnode_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function cnemembershipnode_civicrm_install() {
  _cnemembershipnode_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function cnemembershipnode_civicrm_postInstall() {
  _cnemembershipnode_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function cnemembershipnode_civicrm_uninstall() {
  _cnemembershipnode_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function cnemembershipnode_civicrm_enable() {
  _cnemembershipnode_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function cnemembershipnode_civicrm_disable() {
  _cnemembershipnode_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function cnemembershipnode_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _cnemembershipnode_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function cnemembershipnode_civicrm_managed(&$entities) {
  _cnemembershipnode_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function cnemembershipnode_civicrm_caseTypes(&$caseTypes) {
  _cnemembershipnode_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function cnemembershipnode_civicrm_angularModules(&$angularModules) {
  _cnemembershipnode_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function cnemembershipnode_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _cnemembershipnode_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function cnemembershipnode_civicrm_entityTypes(&$entityTypes) {
  _cnemembershipnode_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function cnemembershipnode_civicrm_themes(&$themes) {
  _cnemembershipnode_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function cnemembershipnode_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function cnemembershipnode_civicrm_navigationMenu(&$menu) {
  _cnemembershipnode_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _cnemembershipnode_civix_navigationMenu($menu);
} // */