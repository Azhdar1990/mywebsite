<?php

namespace YOOtheme\Builder;

class IndexTransform
{
    /**
     * Transform callback.
     *
     * @param object $node
     * @param array  $params
     */
    public function __invoke($node, array &$params)
    {
        if ($params['context'] !== 'render') {
            return;
        }

        if (empty($params['prefix']) || empty($params['parent'])) {
            return;
        }

        $prefix = empty($params['data-id']) ? "{$params['prefix']}#" : "{$params['data-id']}-";

        $node->attrs = $node->attrs ?? [];
        $node->attrs['data-id'] = $params['data-id'] = $prefix . $params['i'];
        $node->attrs['data-element'] = $params['type']->element ?? null;
    }
}
