#!groovy

String phpVersion = "7.1"
String launchUnitTests = "yes"
String launchIntegrationTests = "yes"

stage("Checkout") {
    milestone 1
    if (env.BRANCH_NAME =~ /^PR-/) {
        userInput = input(message: 'Launch tests?', parameters: [
            choice(choices: 'yes\nno', description: 'Run unit tests and code style checks', name: 'launchUnitTests'),
            choice(choices: 'yes\nno', description: 'Run integration tests', name: 'launchIntegrationTests')
        ])

        launchUnitTests = userInput['launchUnitTests']
        launchIntegrationTests = userInput['launchIntegrationTests']
    }
    milestone 2

    node('docker') {
        deleteDir()
        checkout scm
        stash "custom_entity"

        deleteDir()
        checkout([$class: 'GitSCM',
            branches: [[name: '2.0']],
            userRemoteConfigs: [[credentialsId: 'github-credentials', url: 'https://github.com/akeneo/pim-enterprise-standard.git']]
        ])

        stash "pim"
    }

    checkouts = [:];
    checkouts['PIM'] = {
        node('docker') {
            deleteDir()
            cleanUpEnvironment()
            docker.image("akeneo/php:${phpVersion}")
            .inside("-v /home/akeneo/.composer:/home/docker/.composer -e COMPOSER_HOME=/home/docker/.composer") {
                unstash "pim"

                sh "composer update --optimize-autoloader --no-interaction --no-progress --prefer-dist"
                sh "bin/console assets:install"
                sh "bin/console pim:installer:dump-require-paths"

                stash "pim_full"
            }

            sh "mkdir -p /home/akeneo/.yarn-cache"

            docker.image('node:8')
            .inside("-v /home/akeneo/.yarn-cache:/home/node/.yarn-cache -e YARN_CACHE_FOLDER=/home/node/.yarn-cache") {
                unstash "pim_full"

                sh "yarn install"
                sh "yarn run webpack"

                stash "pim_full"
            }

            unstash "pim_full"

            deleteDir()
        }
    }

    parallel checkouts
}

if ('yes' == launchUnitTests) {
    stage("Unit tests and Code style") {
        def tasks = [:]

        tasks["phpspec-7.1"] = {runPhpSpecTest("7.1")}
        tasks["php-cs-fixer"] = {runPhpCsFixerTest()}

        parallel tasks
    }
}

def runPhpSpecTest(phpVersion) {
    node('docker') {
        deleteDir()
        cleanUpEnvironment()
        try {
            docker.image("akeneo/php:${phpVersion}")
            .inside("-v /home/akeneo/.composer:/home/docker/.composer -e COMPOSER_HOME=/home/docker/.composer") {
                unstash "custom_entity"
                sh "composer install --optimize-autoloader --no-interaction --no-progress --prefer-dist"
                sh "mkdir -p app/build/logs/"
                sh "./vendor/bin/phpspec run --no-interaction --format=junit > app/build/logs/phpspec.xml"
            }
        } finally {
            sh "find app/build/logs/ -name \"*.xml\" | xargs sed -i \"s/testcase name=\\\"/testcase name=\\\"[phpspec-${phpVersion}] /\""
            junit "app/build/logs/*.xml"
            deleteDir()
            cleanUpEnvironment()
        }
    }
}

def runPhpCsFixerTest() {
    node('docker') {
        deleteDir()
        cleanUpEnvironment()
        try {
            docker.image("akeneo/php:7.1")
            .inside("-v /home/akeneo/.composer:/home/docker/.composer -e COMPOSER_HOME=/home/docker/.composer") {
                unstash "custom_entity"
                sh "composer install --optimize-autoloader --no-interaction --no-progress --prefer-dist"
                sh "mkdir -p app/build/logs/"
                sh "./vendor/bin/php-cs-fixer fix --diff --dry-run --format=junit --config=.php_cs.php > app/build/logs/phpcs.xml"
            }
        } finally {
            sh "find app/build/logs/ -name \"*.xml\" | xargs sed -i \"s/testcase name=\\\"/testcase name=\\\"[php-cs-fixer] /\""
            junit "app/build/logs/*.xml"
            deleteDir()
            cleanUpEnvironment()
        }
    }
}

def cleanUpEnvironment() {
    deleteDir()
    sh '''
        docker ps -a -q | xargs -n 1 -P 8 -I {} docker rm -f {} > /dev/null
        docker volume ls -q | xargs -n 1 -P 8 -I {} docker volume rm {} > /dev/null
        docker network ls --filter name=akeneo -q | xargs -n 1 -P 8 -I {} docker network rm {} > /dev/null
    '''
}
