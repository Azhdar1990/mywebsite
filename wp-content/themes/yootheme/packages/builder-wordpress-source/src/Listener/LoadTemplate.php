<?php

namespace YOOtheme\Builder\Wordpress\Source\Listener;

use YOOtheme\Builder;
use YOOtheme\Builder\Templates\TemplateHelper;
use YOOtheme\Config;
use YOOtheme\Event;
use YOOtheme\View;

class LoadTemplate
{
    public View $view;
    public Config $config;
    public Builder $builder;
    public TemplateHelper $template;

    public function __construct(
        View $view,
        Config $config,
        Builder $builder,
        TemplateHelper $template
    ) {
        $this->view = $view;
        $this->config = $config;
        $this->builder = $builder;
        $this->template = $template;
    }

    public function handle($tpl)
    {
        $template = Event::emit('builder.template', $tpl);

        if (empty($template['type'])) {
            return $tpl;
        }

        // get template from customizer request?
        $templ = $this->config->get('req.customizer.template');

        if ($this->config->get('app.isCustomizer')) {
            $this->config->set('customizer.view', $template['type']);
        }

        if ($this->view['sections']->exists('builder') && empty($templ)) {
            return $tpl;
        }

        // get visible template
        $visible = $this->template->match($template);

        // set template identifier
        if ($this->config->get('app.isCustomizer')) {
            $this->config->add('customizer.template', [
                'id' => $templ['id'] ?? null,
                'visible' => $visible['id'] ?? null,
            ]);
        }

        if ($templ || $visible) {
            $template += ($templ ?? $visible) + ['layout' => [], 'params' => []];

            if (!empty($template['layout'])) {
                $this->view['sections']->set(
                    'builder',
                    fn() => $this->builder->render(
                        json_encode($template['layout']),
                        $template['params'] + [
                            'prefix' => "template-{$template['id']}",
                        ],
                    ),
                );
            }
        }

        return $tpl;
    }
}
