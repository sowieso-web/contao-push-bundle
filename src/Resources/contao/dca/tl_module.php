<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

$table = 'tl_module';

$GLOBALS['TL_DCA'][$table]['palettes']['push_notification_button'] = '{title_legend},name,type;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID';
