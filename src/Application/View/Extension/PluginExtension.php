<?php

namespace Glpi\Application\View\Extension;

use Glpi\Plugin\Hooks;
use Plugin;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @since 10.0.0
 */
class PluginExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('call_plugin_hook', [$this, 'callPluginHook']),
            new TwigFunction('call_plugin_hook_func', [$this, 'callPluginHookFunction']),
            new TwigFunction('get_plugin_web_dir', [$this, 'getPluginWebDir']),
            new TwigFunction('get_plugins_css_files', [$this, 'getPluginsCssFiles']),
            new TwigFunction('get_plugins_js_scripts_files', [$this, 'getPluginsJsScriptsFiles']),
            new TwigFunction('get_plugins_js_modules_files', [$this, 'getPluginsJsModulesFiles']),
            new TwigFunction('get_plugins_header_tags', [$this, 'getPluginsHeaderTags']),
        ];
    }

    /**
     * Call plugin hook with given params.
     *
     * @param string  $name          Hook name.
     * @param mixed   $params        Hook parameters.
     * @param bool    $return_result Indicates that the result should be returned.
     *
     * @return mixed|void
     */
    public function callPluginHook(string $name, $params = null, bool $return_result = false)
    {
        $result = Plugin::doHook($name, $params);

        if ($return_result) {
            return $result;
        }
    }

    /**
     * Call plugin hook function with given params.
     *
     * @param string  $name          Hook name.
     * @param mixed   $params        Hook parameters.
     * @param bool    $return_result Indicates that the result should be returned.
     *
     * @return mixed|void
     */
    public function callPluginHookFunction(string $name, $params = null, bool $return_result = false)
    {
        $result = Plugin::doHookFunction($name, $params);

        if ($return_result) {
            return $result;
        }
    }

    /**
     * Call Plugin::getWebDir() with given params.
     *
     * @param string  $plugin
     * @param bool    $full
     * @param bool    $use_url_base
     *
     * @return string|null
     */
    public function getPluginWebDir(
        string $plugin,
        bool $full = true,
        bool $use_url_base = false
    ): ?string {
        return Plugin::getWebDir($plugin, $full, $use_url_base) ?: null;
    }

    /**
     * Returns the list of active plugins CSS files.
     *
     * @phpstan-return array<int, array{path: string, options: array{version: string}}>
     */
    public function getPluginsCssFiles(bool $is_anonymous_page): array
    {
        /** @var array $PLUGIN_HOOKS */
        global $PLUGIN_HOOKS;

        $hook = $is_anonymous_page ? Hooks::ADD_CSS_ANONYMOUS_PAGE : Hooks::ADD_CSS;

        $css_files = [];
        if (isset($PLUGIN_HOOKS[$hook]) && count($PLUGIN_HOOKS[$hook])) {
            foreach ($PLUGIN_HOOKS[$hook] as $plugin => $files) {
                if (!Plugin::isPluginActive($plugin)) {
                    continue;
                }

                $plugin_web_dir  = Plugin::getWebDir($plugin, false);
                $plugin_version  = Plugin::getPluginFilesVersion($plugin);

                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
                    $css_files[] = [
                        'path' => "$plugin_web_dir/$file",
                        'options' => [
                            'version' => $plugin_version,
                        ],
                    ];
                }
            }
        }
        return $css_files;
    }

    /**
     * Returns the list of active plugins JS scripts files.
     *
     * @phpstan-return array<int, array{path: string, options: array{version: string}}>
     */
    public function getPluginsJsScriptsFiles(bool $is_anonymous_page): array
    {
        /** @var array $PLUGIN_HOOKS */
        global $PLUGIN_HOOKS;

        $hook = $is_anonymous_page ? Hooks::ADD_JAVASCRIPT_ANONYMOUS_PAGE : Hooks::ADD_JAVASCRIPT;

        $js_files = [];
        if (isset($PLUGIN_HOOKS[$hook]) && count($PLUGIN_HOOKS[$hook])) {
            foreach ($PLUGIN_HOOKS[$hook] as $plugin => $files) {
                if (!Plugin::isPluginActive($plugin)) {
                    continue;
                }
                $plugin_root_dir = Plugin::getPhpDir($plugin, true);
                $plugin_web_dir  = Plugin::getWebDir($plugin, false);
                $plugin_version  = Plugin::getPluginFilesVersion($plugin);

                if (!is_array($files)) {
                    $files = [$files];
                }
                foreach ($files as $file) {
                    if (file_exists($plugin_root_dir . "/{$file}")) {
                        $js_files[] = [
                            'path' => $plugin_web_dir . "/{$file}",
                            'options' => [
                                'version' => $plugin_version,
                            ],
                        ];
                    } else {
                        trigger_error("{$file} file not found from plugin $plugin!", E_USER_WARNING);
                    }
                }
            }
        }
        return $js_files;
    }

    /**
     * Returns the list of active plugins JS modules files.
     *
     * @phpstan-return array<int, array{path: string, options: array{version: string}}>
     */
    public function getPluginsJsModulesFiles(bool $is_anonymous_page): array
    {
        /** @var array $PLUGIN_HOOKS */
        global $PLUGIN_HOOKS;

        $hook = $is_anonymous_page ? Hooks::ADD_JAVASCRIPT_MODULE_ANONYMOUS_PAGE : Hooks::ADD_JAVASCRIPT_MODULE;

        $js_modules = [];
        if (isset($PLUGIN_HOOKS[$hook]) && count($PLUGIN_HOOKS[$hook])) {
            foreach ($PLUGIN_HOOKS[$hook] as $plugin => $files) {
                if (!Plugin::isPluginActive($plugin)) {
                    continue;
                }
                $plugin_root_dir = Plugin::getPhpDir($plugin, true);
                $plugin_web_dir  = Plugin::getWebDir($plugin, false);
                $plugin_version  = Plugin::getPluginFilesVersion($plugin);

                if (!is_array($files)) {
                    $files = [$files];
                }
                foreach ($files as $file) {
                    if (file_exists($plugin_root_dir . "/{$file}")) {
                        $js_modules[] = [
                            'path' => $plugin_web_dir . "/{$file}",
                            'options' => [
                                'version' => $plugin_version,
                            ],
                        ];
                    } else {
                        trigger_error("{$file} file not found from plugin $plugin!", E_USER_WARNING);
                    }
                }
            }
        }
        return $js_modules;
    }

    /**
     * Returns the list of active plugins header tags.
     *
     * @phpstan-return array<int, array{tag: string, properties: array<string, string>}>
     */
    public function getPluginsHeaderTags(bool $is_anonymous_page): array
    {
        /** @var array $PLUGIN_HOOKS */
        global $PLUGIN_HOOKS;

        $hook = $is_anonymous_page ? Hooks::ADD_HEADER_TAG_ANONYMOUS_PAGE : Hooks::ADD_HEADER_TAG;

        $header_tags = [];
        if (isset($PLUGIN_HOOKS[$hook]) && count($PLUGIN_HOOKS[$hook])) {
            foreach ($PLUGIN_HOOKS[$hook] as $plugin => $plugin_header_tags) {
                if (!Plugin::isPluginActive($plugin)) {
                    continue;
                }
                array_push($header_tags, ...$plugin_header_tags);
            }
        }
        return $header_tags;
    }
}
