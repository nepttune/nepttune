<?php

namespace App\Presenter;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{
    /** @persistent */
    public $lang;

    /** @var  array */
    public $locale;

    /** @var  array */
    protected $styles = [];

    /** @var  array */
    protected $scripts = [
        '/bower/jquery/dist/jquery.min.js',
        '/bower/nette.ajax.js/nette.ajax.js',
        '/bower/nette-forms/src/assets/netteForms.min.js',
        '/bower/selectize/dist/js/standalone/selectize.min.js',
        '/bower/iCheck/icheck.min.js',
        '/bower/magnific-popup/dist/jquery.magnific-popup.min.js',
        '/bower/chart.js/dist/Chart.min.js',
        '/js/selectizePlugins.min.js',
        '/js/coreValidator.min.js',
        '/js/common.min.js'
    ];

    public function addStyle(string $path)
    {
        if (!in_array($path, $this->styles, true))
        {
            $this->styles[] = $path;
        }
    }

    public function addScript(string $path)
    {
        if (!in_array($path, $this->scripts, true))
        {
            $this->scripts[] = $path;
        }
    }

    public function getStyles() : array
    {
        return $this->styles;
    }

    public function getScripts() : array
    {
        return $this->scripts;
    }

    public function startup()
    {
        parent::startup();

        $this->autoCanonicalize = false;

        switch ((string) $this->lang)
        {
            default:
                $this->lang = 'en';
            case 'en':
            case 'cs':
            case 'de':
            case 'es':
            case 'fr':
            case 'ru':
                $this->locale = require __DIR__ . "/../locale/{$this->lang}.php";
                break;
        }
    }

    public function flashMessage($message, $type = 'info') : \stdClass
    {
        $flash = parent::flashMessage($message, $type);

        if ($this->isAjax())
        {
            $this->redrawControl('flashMessages');
        }

        return $flash;
    }

    public function createComponent($name, array $args = null)
    {
        if (method_exists($this, 'createComponent'.ucfirst($name)))
        {
            return parent::createComponent($name);
        }

        if ($args !== null)
        {
            return $this->context->createService($name, $args);
        }

        return $this->context->createService($name);
    }

    public function getPost()
    {
        return $this->getHttpRequest()->getPost();
    }

    public function getRemoteAddress()
    {
        return $this->getHttpRequest()->getRemoteAddress();
    }

    public function getId() : int
    {
        return (int) $this->getParameter('id');
    }

    public function handleRedrawControl(string $control = null)
    {
        if ($control && $this->isAjax())
        {
            if (method_exists($this, 'redraw'.ucfirst($control)))
            {
                $this->{'redraw'.ucfirst($control)}();
            }

            $this->redrawControl($control);
        }
    }

    public function handleRedrawRow(string $control = null, int $rowId = null)
    {
        if ($this->isAjax() && $control && $rowId)
        {
            /** @var \Nextras\Datagrid\Datagrid $grid */
            $grid = $this[$control]['dataGrid'];

            $grid->redrawRow($rowId);
        }
    }

    public function actionCloseFancy($control = null, $rowId = null)
    {
        $this->getFlashSession()->setExpiration(time() + 5);

        $this->template->setFile(__DIR__.'/closeFancy.latte');

        $this->template->redrawControl = false;
        $this->template->redrawRow = false;

        if ($control && $rowId)
        {
            $this->template->redrawRow = true;
            $this->template->control = $control;
            $this->template->rowId = $rowId;
        }
        elseif ($control)
        {
            $this->template->redrawControl = true;
            $this->template->control = $control;
        }
    }

    public function findLayoutTemplateFile() : string
    {
        $dir = dirname(self::getReflection()->getFileName());
        $primary = $dir . '/../templates/@layout.latte';

        if (is_file($primary))
        {
            return $primary;
        }

        return self::getDefaultLayout();
    }

    public static function getDefaultLayout() : string
    {
        return __DIR__ . '/../templates/@layout.latte';
    }

    public static function getAjaxLayout() : string
    {
        return __DIR__ . '/../templates/@ajax.latte';
    }

    public static function getIframeLayout() : string
    {
        return __DIR__ . '/../templates/@iframe.latte';
    }

    public static function getCoreLayout() : string
    {
        return __DIR__ .'/../templates/@core.latte';
    }

    public static function generateChecksum(string $path) : string
    {
        return 'sha256-' . base64_encode(hash_file('sha256', $path, true));
    }

    public function getModule() : string
    {
        return substr($this->getName(), 0, strpos($this->getName(), ':'));
    }
}
