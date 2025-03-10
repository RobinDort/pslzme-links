<?php
namespace RobinDort\PslzmeLinks\Backend;

use Contao\BackendModule;
use Contao\BackendTemplate;
use Contao\PageTree;
use Contao\Input;

use RobinDort\PslzmeLinks\Service\Backend\DatabasePslzmeConfigStmtExecutor;

class PslzmeConfiguration extends BackendModule {

    protected $strTemplate = "be_pslzme_configuration";

    private $pslzmeDBName;
    private $pslzmeDBUser;
    private $pslzmeDBIPR;

    private $dbPslzmeStmtExecutor;

    public function __construct($dbPslzmeStmtExecutor = null) {
        parent::__construct();
    }


    public function setContainer($container) {
        parent::setContainer($container);

        // Retrieve the service from the container
        $this->dbPslzmeStmtExecutor = $this->container->get('RobinDort\\PslzmeLinks\\Service\\Backend\\DatabasePslzmeConfigStmtExecutor');

        $databaseData = $this->dbPslzmeStmtExecutor->selectCurrentDatabaseConfigurationData();
        if (!empty($databaseData)) {
            $this->pslzmeDBName = $databaseData["databaseName"];
            $this->pslzmeDBUser = $databaseData["databaseUser"];
            $this->pslzmeDBIPR = $databaseData["databaseIPR"];
        }
    }


    /**
     * {@inheritDoc}
     */
    public function compile() {}

    public function generate() {
        $this->Template = new BackendTemplate($this->strTemplate);
        $this->Template->pslzmeDBName = $this->pslzmeDBName;
        $this->Template->pslzmeDBUser = $this->pslzmeDBUser;

        if(!empty($this->pslzmeDBIPR)) {
            $decodedPages = json_decode($this->pslzmeDBIPR,true);
            $imprintID = $decodedPages["Imprint"];
            $privacyID = $decodedPages["Privacy"];
            $homeID = $decodedPages["Home"];

            $this->Template->imprintID = $imprintID;
            $this->Template->privacyID = $privacyID;
            $this->Template->homeID = $homeID;
        }

        $this->compile();

        return $this->Template->parse();
    }
}
?>