<?php
/**
 * SiteModule module for Craft CMS 3.x
 *
 * An example module for Craft CMS 3 that lets you enhance your websites with a custom site module
 *
 * @link      https://byhoffman.com/
 * @copyright Copyright (c) 2018 byhoffman
 */

namespace modules\sitemodule\variables;

use modules\sitemodule\SiteModule;

use angellco\spoon\Spoon;
use angellco\spoon\models\BlockType;
use angellco\spoon\records\BlockType as BlockTypeRecord;
use angellco\spoon\errors\BlockTypeNotFoundException;

use Craft;

/**
 * SiteModule Variable
 *
 * Craft allows modules to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.siteModule }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    byhoffman
 * @package   SiteModule
 * @since     1.0.0
 */
class SiteModuleVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.siteModule.getComponentsGroupName }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.siteModule.getComponentsGroupName(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function getComponentsGroupName($context = false, $fieldId = false)
    {
        if (!$fieldId || !$context) {
            return [];
        }

        // Get any existing field layouts so we don’t lose them
        $groupNameList = [];
        $compareList = [];
        $fieldLayoutIds = Spoon::$plugin->blockTypes->getFieldLayoutIds($context, $fieldId);

        foreach ($fieldLayoutIds as $matrixTypeId => $unused) {

            $condition['matrixBlockTypeId'] = $matrixTypeId;
            $groupName = BlockTypeRecord::findAll($condition)[0]->groupName;

            if ($groupName && !in_array($groupName, $compareList)) {
                $groupNameList[$matrixTypeId] = $groupName;
            }

            $compareList[$matrixTypeId] = $groupName;
        }

        return $groupNameList;
    }

    /**
     *
     */
    public function getComponentGroupName($matrixTypeId)
    {
        $condition['matrixBlockTypeId'] = $matrixTypeId;
        $groupName = BlockTypeRecord::findAll($condition)[0]->groupName;

        return $groupName;
    }

    public function getCurrentBranch()
    {
        $stringfromfile = file(CRAFT_BASE_PATH . '/.git/HEAD', FILE_USE_INCLUDE_PATH);
        $firstLine = $stringfromfile[0]; //get the string from the array
        $explodedstring = explode("/", $firstLine, 3); //seperate out by the "/" in the string
        $branchname = $explodedstring[2]; //get the one that is always the branch name

        return $branchname;
    }
}
