<?php

namespace Klevu\FrontendJs\Service;

class JsIncludesSorter
{
    /**
     * @param array $jsIncludes
     * @return array
     */
    public function execute(array $jsIncludes)
    {
        $position = 0;
        array_walk($jsIncludes, static function (&$jsInclude) use (&$position) {
            $jsInclude['position'] = $position += 100;
        });

        $return = array_filter($jsIncludes, static function ($jsInclude) {
            return is_array($jsInclude);
        });

        // We run twice to catch relative ordering on before / after "-" items
        //  eg, if the last item is intended to be first (before = "-") and an earlier item is set
        //  after then we need to reevaluate the earlier item's position based on the new position
        //  of before = "-"
        for ($j=0; $j<2; $j++) {
            foreach ($jsIncludes as $identifier => $jsInclude) {
                $before = isset($jsInclude['before']) ? $jsInclude['before'] : null;
                switch (true) {
                    case null === $before:
                        break;

                    case '-' === $before:
                        $return[$identifier]['position'] = 0;
                        break;

                    case isset($return[$before]):
                        $return[$identifier]['position'] = $return[$before]['position'] - 50;
                        break;
                }

                $after = isset($jsInclude['after']) ? $jsInclude['after'] : null;
                switch (true) {
                    case null === $after:
                        break;

                    case '-' === $after:
                        $return[$identifier]['position'] = max(array_column($return, 'position'));
                        break;

                    case isset($return[$after]):
                        $return[$identifier]['position'] = $return[$after]['position'] + 25;
                        break;
                }
            }
        }

        usort($return, static function ($a, $b) {
            switch (true) {
                case $a['position'] < $b['position']:
                    $return = -1;
                    break;

                case $a['position'] > $b['position']:
                    $return = 1;
                    break;

                case $a['position'] === $b['position']:
                default:
                    $return = 0;
                    break;
            }

            return $return;
        });

        array_walk($return, static function (&$jsInclude) use (&$position) {
            unset($jsInclude['position']);
        });

        return $return;
    }
}
