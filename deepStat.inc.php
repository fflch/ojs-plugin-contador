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

            if (isset($config['database'])) {
                $this->databaseHost = $config['database']['host'];
                $this->databaseName = $config['database']['name'];
                $this->databaseUsername = $config['database']['username'];
                $this->databasePassword = $config['database']['password'];
            }
        }

		
        $revistas = $this->obterDados(); // Chama a função para obter a lista de revistas.
    
    $templateMgr->assign([
        'madeByText' => 'Made with ❤ by the Public Knowledge Project',
        'revistas' => implode(', ', $revistas), // Converte a lista de revistas em uma string
    ]);
    
    return parent::getContents($templateMgr, $request);
    }

	public function obterDados() {
        try {
            $pdo = new PDO("mysql:host={$this->databaseHost};dbname={$this->databaseName}", $this->databaseUsername, $this->databasePassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Agora você pode fazer uma consulta SQL para obter a lista de revistas.
            $query = "SELECT path FROM journals"; // Altere a consulta conforme necessário.
            $stmt = $pdo->query($query);
            $revistas = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
            // Verifica se há resultados
            if (!empty($revistas)) {
                return $revistas;
            } else {
                return "Nenhuma revista encontrada no banco de dados.";
            }
        } catch (PDOException $e) {
            return "Erro ao conectar ao banco de dados: " . $e->getMessage();
        }
    }

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

	/**
	 * Get the display name of this plugin.
	 * @return String
	 */
	function getDisplayName() {
		return __('Deep Statistics');
	}

	/**
	 * Get a description of the plugin.
	 */
	function getDescription() {
		return __('Deep Statistics');
	}
}


