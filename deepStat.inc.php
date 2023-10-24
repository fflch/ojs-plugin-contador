<?php

/**
 * @file plugins/blocks/deepStat/deepStat.inc.php
 */



import('lib.pkp.classes.plugins.BlockPlugin');

class deepStat extends BlockPlugin {
	//variaveis do banco de dados
    private $databaseHost;
    private $databaseName;
    private $databaseUsername;
    private $databasePassword;


	public function getContents($templateMgr, $request = null)
    {

		$configFile = 'config.inc.php';

        if (file_exists($configFile)) {
            $config = parse_ini_file($configFile, true);
            //acesso ao banco de dados
            if (isset($config['database'])) {
                $this->databaseHost = $config['database']['host'];
                $this->databaseName = $config['database']['name'];
                $this->databaseUsername = $config['database']['username'];
                $this->databasePassword = $config['database']['password'];
            }
        }

		// Chama a função para obter o número de revistas.
        $totalRevistas = $this->obterDados();     
        
        $templateMgr->assign([
        // Variável com texto simples.
        'madeByText' => 'Made with ❤ by the Public Knowledge Project',
        // Variável que contém o número de revistas.
        'totalRevistas' => $totalRevistas, 
    ]);
    
    return parent::getContents($templateMgr, $request);
    }

    //funcao que pega o numero de revistas
	public function obterDados() {
        try {
            $pdo = new PDO("mysql:host={$this->databaseHost};dbname={$this->databaseName}", $this->databaseUsername, $this->databasePassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $query = "SELECT COUNT(*) as total FROM journals"; // Conta o número de revistas.
            $stmt = $pdo->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalRevistas = $result['total'];
    
            return $totalRevistas;
        } catch (PDOException $e) {
            return "Erro ao conectar ao banco de dados: " . $e->getMessage();
        }
    }













//////funcoes obrigatorias do ojs
	/**
	 * Install default settings on system install.
	 * @return string
	 */
	function getInstallSitePluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}
	/**
	 * Install default settings on journal creation.
	 * @return string
	 */
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	function getDisplayName() {
		return __('Deep Statistics');
	}
    function getDescription() {
		return __('Deep Statistics');
	}
}


