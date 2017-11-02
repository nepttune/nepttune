<?php

namespace Peldax\NetteInit\Presenter;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{
    use \IPub\MobileDetect\TMobileDetect;

    /** @persistent */
    public $locale;

    /**
     * @var \Kdyby\Translation\Translator
     * @inject
     */
    public $translator;

    /**
     * @var \Kdyby\Redis\RedisStorage
     * @inject
     */
    public $storage;

    public function getIntegrity(string $path) : string
    {
        $cache = new \Nette\Caching\Cache($this->storage);

        return $cache->call('Peldax\NetteInit\Presenter\BasePresenter::generateChecksum', $path);
    }

    public static function generateChecksum(string $path) : string
    {
        return 'sha256-' . base64_encode(hash_file('sha256', $path, true));
    }

    public function startup()
    {
        parent::startup();

        $this->autoCanonicalize = false;
    }

    public function beforeRender()
    {
        parent::beforeRender();

        $this->template->appName = $this->context->parameters['appName'];
        $this->template->appNameShort = $this->context->parameters['appNameShort'];
        $this->template->appDescription = $this->context->parameters['appDescription'];
        $this->template->appKeywords = $this->context->parameters['appKeywords'];
        $this->template->appAuthor = $this->context->parameters['appAuthor'];
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

    public function handleRedrawControl(string $control = null) : void
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

    public function handleRedrawRow(string $control = null, int $rowId = null) : void
    {
        if ($this->isAjax() && $control && $rowId)
        {
            $grid = $this[$control]['dataGrid'];

            $grid->redrawRow($rowId);
        }
    }

    public function actionCloseFancy($control = null, $rowId = null) : void
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

        return static::getDefaultLayout();
    }

    public static function getDefaultLayout() : string
    {
        return static::getCoreLayout();
    }

    public static function getAdminLayout() : string
    {
        return __DIR__ . '/../templates/@admin.latte';
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

    public function getModule() : string
    {
        return substr($this->getName(), 0, strpos($this->getName(), ':'));
    }

    public function getNameWM() : string
    {
        return substr($this->getName(), strpos($this->getName(), ':') + 1);
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
}
