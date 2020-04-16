<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
 */

$table = 'tl_module';

$GLOBALS['TL_DCA'][$table]['palettes']['push_notification_button'] = '{title_legend},name,type;{protected_legend:hide},protected;{expert_legend:hide},cssID';
