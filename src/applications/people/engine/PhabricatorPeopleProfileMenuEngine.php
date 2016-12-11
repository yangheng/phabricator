<?php

final class PhabricatorPeopleProfileMenuEngine
  extends PhabricatorProfileMenuEngine {

  const PANEL_PROFILE = 'people.profile';
  const PANEL_MANAGE = 'people.manage';

  protected function isMenuEngineConfigurable() {
    return false;
  }

  protected function getPanelURI($path) {
    $user = $this->getProfileObject();
    $username = $user->getUsername();
    $username = phutil_escape_uri($username);
    return "/p/{$username}/panel/{$path}";
  }

  protected function getBuiltinProfilePanels($object) {
    $viewer = $this->getViewer();

    $panels = array();

    $panels[] = $this->newPanel()
      ->setBuiltinKey(self::PANEL_PROFILE)
      ->setMenuItemKey(PhabricatorPeopleDetailsProfilePanel::PANELKEY);

    $have_maniphest = PhabricatorApplication::isClassInstalledForViewer(
      'PhabricatorManiphestApplication',
      $viewer);
    if ($have_maniphest) {
      $uri = urisprintf(
        '/maniphest/?statuses=open()&assigned=%s#R',
        $object->getPHID());

      $panels[] = $this->newPanel()
        ->setBuiltinKey('tasks')
        ->setMenuItemKey(PhabricatorLinkProfilePanel::PANELKEY)
        ->setMenuItemProperty('icon', 'maniphest')
        ->setMenuItemProperty('name', pht('Open Tasks'))
        ->setMenuItemProperty('uri', $uri);
    }

    $have_differential = PhabricatorApplication::isClassInstalledForViewer(
      'PhabricatorDifferentialApplication',
      $viewer);
    if ($have_differential) {
      $uri = urisprintf(
        '/differential/?authors=%s#R',
        $object->getPHID());

      $panels[] = $this->newPanel()
        ->setBuiltinKey('revisions')
        ->setMenuItemKey(PhabricatorLinkProfilePanel::PANELKEY)
        ->setMenuItemProperty('icon', 'differential')
        ->setMenuItemProperty('name', pht('Revisions'))
        ->setMenuItemProperty('uri', $uri);
    }

    $have_diffusion = PhabricatorApplication::isClassInstalledForViewer(
      'PhabricatorDiffusionApplication',
      $viewer);
    if ($have_diffusion) {
      $uri = urisprintf(
        '/audit/?authors=%s#R',
        $object->getPHID());

      $panels[] = $this->newPanel()
        ->setBuiltinKey('commits')
        ->setMenuItemKey(PhabricatorLinkProfilePanel::PANELKEY)
        ->setMenuItemProperty('icon', 'diffusion')
        ->setMenuItemProperty('name', pht('Commits'))
        ->setMenuItemProperty('uri', $uri);
    }

    $panels[] = $this->newPanel()
      ->setBuiltinKey(self::PANEL_MANAGE)
      ->setMenuItemKey(PhabricatorPeopleManageProfilePanel::PANELKEY);

    return $panels;
  }

}