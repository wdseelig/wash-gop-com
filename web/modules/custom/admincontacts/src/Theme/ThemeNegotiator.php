<?php
/**
 * @file
 * Contains \Drupal\jcmodule\Theme\ThemeNegotiator
 */
namespace Drupal\admincontacts\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

class ThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * @param RouteMatchInterface $route_match
   * @return bool
   */
  public function applies(RouteMatchInterface $route_match)
  {
    return $this->negotiateRoute($route_match) ? true : false;
  }

  /**
   * @param RouteMatchInterface $route_match
   * @return null|string
   */
  public function determineActiveTheme(RouteMatchInterface $route_match)
  {
    return $this->negotiateRoute($route_match) ?: null;
  }

  /**
   * Function that does all of the work in selecting a theme
   * @param RouteMatchInterface $route_match
   * @return bool|string
   */
  private function negotiateRoute(RouteMatchInterface $route_match)
  {
    $acmain = \Drupal::request()->query->get('acmain');
    $pagepath = $route_match->getRouteName();
    if (strpos($pagepath, "contactdatav5") || strpos($pagepath, "cdv8")){
      return 'cdview';
    }
    $route_name = $route_match->getRouteName();
    if ($route_match->getRouteName() == 'admincontacts.home')
    {
      return 'acmain';
    }
    if ($route_match->getRouteName() == 'entity.contactdata.edit_form' && (empty($acmain) || ($acmain !=="0")))
    {
      return 'acmain';
    }
    if ($route_match->getRouteName() == 'entity.contactdata.add_form' && (empty($acmain) || ($acmain !== "0")))
    {
      return 'acmain';
    }
    return false;
  }

}
