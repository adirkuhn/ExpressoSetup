<?php
/**
*
* Copyright (C) 2011 Consórcio Expresso Livre - 4Linux (www.4linux.com.br) e Prognus Software Livre (www.prognus.com.br)
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
* any later version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT
* ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
* details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software Foundation,
* Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
*
* You can contact Prognus Software Livre headquarters at Av. Tancredo Neves,
* 6731, PTI, Edifício do Saber, 3º floor, room 306, Foz do Iguaçu - PR - Brasil
* or at e-mail address prognus@prognus.com.br.
*
* Gerenciamento de aplicativos do Expresso
*
* @license    http://www.gnu.org/copyleft/gpl.html GPL
* @author     Prognus Software Livre (www.prognus.com.br)
* @version    1.0
* @sponsor    Prognus Software Livre (www.prognus.com.br)
* @since      Arquivo disponibilizado na versão dev-master
*/

namespace Expresso\AppManagerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\ORM\EntityManager;

/**
* Classe do bundle AppManager, responsável por carregar o modulo no AppKernel do Symfony e também os apps (modulos) instalados no Expresso
* 
* @author  Adir Kuhn <adirkuhn@gmail.com>
* @license http://www.gnu.org/copyleft/gpl.html GPL
* @version 1.0
* @since   Classe disponibilizada na versão 1.0 
*/
class AppManagerBundle extends Bundle
{

    /**
    * Lê arquivo composer.lock e retorna os Apps Instalados no Expresso
    *
    * @license http://www.gnu.org/copyleft/gpl.html GPL
    * @author  Adir Kuhn <adirkuhn@gmail.com>
    * @return  <array>
    * @access  <public>
    */
    public function getApps()
    {
        $installedPackages = array();
        $composerLock = __DIR__ . '/AppManager/composer.lock';

        if (file_exists($composerLock)) {
            $content = json_decode(file_get_contents($composerLock));
            foreach ($content->packages as $key => $package) {
                if ($package->name === 'expresso/appmanager-installer') {
                    continue;
                } else {
                    $pkgName = 'ExpressoApps\\';
                    $pkgName .= str_replace('/', '\\', $package->{'target-dir'});
                    $pkgName .= '\\' . substr($package->{'target-dir'}, (strpos($package->{'target-dir'}, '/') + 1), strlen($package->{'target-dir'}));
                    $installedPackages[] = $pkgName;
                    unset($pkgName);
                }
            }
        }

        return $installedPackages;
    }
}
