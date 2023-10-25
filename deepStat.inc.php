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
        $totalRevistas = $this->totalJournals(); 
        $totalIssues = $this->totalIssues(); 
        $totalArticles = $this->totalArticles();  
        $totalAcess = $this->totalAcess();   
        $totalDownloads = $this->totalDownloads();     
        
        $templateMgr->assign([
        // Variável com texto simples.
        'madeByText' => 'Estatísticas do portal:',
        // Variável que contém o número de revistas.
        'totalRevistas' => $totalRevistas, 
        'totalIssues' =>$totalIssues,
        'totalArticles' =>$totalArticles,
        'totalAcess' =>$totalAcess,
        'totalDownloads' =>$totalDownloads,
    ]);
    
    return parent::getContents($templateMgr, $request);
    }

//funcao que pega o numero de revistas
	public function totalJournals() {
        try {
            $pdo = new PDO("mysql:host={$this->databaseHost};dbname={$this->databaseName}", $this->databaseUsername, $this->databasePassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Conta o número de revistas ativas.
            $query = "SELECT COUNT(*) as total FROM journals WHERE enabled = 1"; 
            $stmt = $pdo->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalRevistas = $result['total'];
    
            return $totalRevistas;
        } catch (PDOException $e) {
            return "Erro ao conectar ao banco de dados: " . $e->getMessage();
        }
    }

//funcao que pega o numero de fasciculos (issue)
    public function totalIssues() {
        try {
            $pdo = new PDO("mysql:host={$this->databaseHost};dbname={$this->databaseName}", $this->databaseUsername, $this->databasePassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Conta o número de fascículos publicados.
            $query = "SELECT COUNT(*) as total FROM issues WHERE published = 1"; 
            $stmt = $pdo->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalIssues = $result['total'];

            return $totalIssues;
        } catch (PDOException $e) {
            return "Erro ao conectar ao banco de dados: " . $e->getMessage();
        }
    }

//funcao que pega o numero de Artigos publicados
    public function totalArticles() {
        try {
            $pdo = new PDO("mysql:host={$this->databaseHost};dbname={$this->databaseName}", $this->databaseUsername, $this->databasePassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Conta o número de artigos publicados.
            $query = "SELECT COUNT(*) as total FROM publications WHERE status = 3"; 
    // ou $query = "SELECT COUNT(*) as total FROM submissions WHERE status = 3";
            $stmt = $pdo->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalArticles = $result['total'];

            return $totalArticles;
        } catch (PDOException $e) {
            return "Erro ao conectar ao banco de dados: " . $e->getMessage();
        }
    }

    //funcao que pega o numero total de downloads ao portal
public function totalDownloads() {
    try {
        $pdo = new PDO("mysql:host={$this->databaseHost};dbname={$this->databaseName}", $this->databaseUsername, $this->databasePassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Soma os valores da coluna 'metric' onde 'assoc_type' contém 256 ou 1048585.
//515 = downloads
        $query = "SELECT SUM(metric) as total FROM metrics WHERE assoc_type IN (515)"; 
        $stmt = $pdo->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalDownloads = $result['total'];

        return $totalDownloads;
    } catch (PDOException $e) {
        return "Erro ao conectar ao banco de dados: " . $e->getMessage();
    }
}

//funcao que pega o numero total de acessos ao portal
    public function totalAcess() {
        try {
            $pdo = new PDO("mysql:host={$this->databaseHost};dbname={$this->databaseName}", $this->databaseUsername, $this->databasePassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Soma os valores da coluna 'metric' onde 'assoc_type' contém 256 ou 1048585.
    //256 = visitas ao home de cada revista
    //1048585 = visitas as páginas de artigos
            $query = "SELECT SUM(metric) as total FROM metrics WHERE assoc_type IN (256, 1048585)"; 
            $stmt = $pdo->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalAcess = $result['total'];

            return $totalAcess;
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
		return __('plugins.block.deepStat.displayName');
	}
    function getDescription() {
		return __('plugins.block.deepStat.description');
	}
}


