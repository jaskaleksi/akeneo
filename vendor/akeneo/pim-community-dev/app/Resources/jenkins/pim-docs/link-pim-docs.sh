#!/bin/bash

# add Akeneo/CustomEntityBundle to composer.json
php composer.phar require akeneo/custom-entity-bundle dev-master --no-update

# clone pim-docs repository
git clone https://github.com/akeneo/pim-docs.git

# create symlink for Acme pim-docs bundles
cd src
ln -s ../pim-docs/src/Acme Acme
cd ..

# update AppKernel
sed -i 's/PimEnrichBundle(),/PimEnrichBundle(),new Pim\\Bundle\\CustomEntityBundle\\PimCustomEntityBundle(),new Acme\\Bundle\\CatalogBundle\\AcmeCatalogBundle(),new Acme\\Bundle\\DemoConnectorBundle\\AcmeDemoConnectorBundle(),new Acme\\Bundle\\EnrichBundle\\AcmeEnrichBundle(),new Acme\\Bundle\\InstallerBundle\\AcmeInstallerBundle(),new Acme\\Bundle\\SpecificConnectorBundle\\AcmeSpecificConnectorBundle(),/' app/AppKernel.php

# update routing.yml
echo "pim_customentity:" >> app/config/routing.yml
echo "    prefix: /enrich" >> app/config/routing.yml
echo "    resource: \"@PimCustomEntityBundle/Resources/config/routing.yml\"" >> app/config/routing.yml

