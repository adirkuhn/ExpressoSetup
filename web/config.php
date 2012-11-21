<?php

if (!isset($_SERVER['HTTP_HOST'])) {
    exit('This script cannot be run from the CLI. Run it from a browser.');
}

/*if (!in_array(@$_SERVER['REMOTE_ADDR'], array(
    '127.0.0.1',
    '::1',
))) {
    header('HTTP/1.0 403 Forbidden');
    exit('This script is only accessible from localhost.');
}*/

require_once dirname(__FILE__).'/../app/SymfonyRequirements.php';

$symfonyRequirements = new SymfonyRequirements();

$majorProblems = $symfonyRequirements->getFailedRequirements();
$minorProblems = $symfonyRequirements->getFailedRecommendations();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <link rel="stylesheet" href="bundles/sensiodistribution/webconfigurator/css/install.css" media="all" />
        <title>Configurações do Expresso</title>
    </head>
    <body>
        <div id="symfony-wrapper">
            <div id="symfony-content">
                <div class="symfony-blocks-install">
                    <div class="symfony-block-logo">
                        <img src="bundles/sensiodistribution/webconfigurator/images/login_expresso.png" width="200" heidth="200" alt="Symfony logo" />
                    </div>

                    <div class="symfony-block-content">
                        <h1>Olá!</h1>
                        <p>Bem vindo ao Expresso Livre.</p>
                        <p>
                            Este script irá guiá-lo através da configuração básica do seu projeto. 
							Você também pode fazer o mesmo pela edição do arquivo ‘<strong>app/config/parameters.yml</strong>’ diretamente.
                        </p>

                        <?php if (count($majorProblems)): ?>
                            <h2 class="ko">Principais Problemas</h2>
                            <p>Alguns problemas foram detectados e <strong>precisam</strong> serem corrigidos antes de continuar:</p>
                            <ol>
                                <?php foreach ($majorProblems as $problem): ?>
                                    <li><?php echo $problem->getHelpHtml() ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>

                        <?php if (count($minorProblems)): ?>
                            <h2>Recomendações</h2>
                            <p>
                                <?php if (count($majorProblems)): ?>							
								Além disso, para<?php else: ?>Para<?php endif; ?> melhorar a sua experiência com o Expresso,
                                é recomendado que você corrigir o seguinte:
                            </p>
                            <ol>
                                <?php foreach ($minorProblems as $problem): ?>
                                    <li><?php echo $problem->getHelpHtml() ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>

                        <?php if ($symfonyRequirements->hasPhpIniConfigIssue()): ?>
                            <p id="phpini">*
                                <?php if ($symfonyRequirements->getPhpIniConfigPath()): ?>
                                    Modificações no arquivo <strong>php.ini</strong> devem ser feitas em "<strong><?php echo $symfonyRequirements->getPhpIniConfigPath() ?></strong>".
                                <?php else: ?>
                                    Para modificar suas configurações, crie um "<strong>php.ini</strong>".
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!count($majorProblems) && !count($minorProblems)): ?>
                            <p class="ok">Sua configuração parece boa para executar o Expresso Livre.</p>
                        <?php endif; ?>

                        <ul class="symfony-install-continue">
                            <?php if (!count($majorProblems)): ?>
                                <li><a href="app_dev.php/_configurator/">Configure o Expresso Online</a></li>
                                <li><a href="app_dev.php/">Ignora a configuração e ir para a página inicial</a></li>
                            <?php endif; ?>
                            <?php if (count($majorProblems) || count($minorProblems)): ?>
                                <li><a href="config.php">Verificar configurações novamente</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
