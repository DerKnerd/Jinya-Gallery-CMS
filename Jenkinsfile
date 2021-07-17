// Uses Declarative syntax to run commands inside a container.
pipeline {
    triggers {
        pollSCM("*/5 * * * *")
    }
    agent {
        kubernetes {
            yaml '''
apiVersion: v1
kind: Pod
spec:
  volumes:
    - name: docker-sock
      hostPath:
        path: /var/run/docker.sock
  containers:
    - name: php
      image: quay.imanuel.dev/dockerhub/library---php:8-apache
      command:
        - sleep
      args:
        - infinity
    - name: package
      image: quay.imanuel.dev/dockerhub/library---golang:latest
      command:
        - sleep
      args:
        - infinity
    - name: docker
      image: quay.imanuel.dev/dockerhub/library---docker:stable
      command:
        - cat
      tty: true
      volumeMounts:
        - mountPath: /var/run/docker.sock
          name: docker-sock
    - name: mysql
      image: quay.imanuel.dev/dockerhub/library---mysql:8
      ports:
        - containerPort: 3306
      args:
        - --transaction-isolation=READ-COMMITTED
        - --binlog-format=ROW
        - --max-connections=1000
        - --bind-address=0.0.0.0
      env:
        - name: MYSQL_DATABASE
          value: jinya
        - name: MYSQL_PASSWORD
          value: jinya
        - name: MYSQL_ROOT_PASSWORD
          value: jinya
        - name: MYSQL_USER
          value: jinya
    - name: mailhog
      image: quay.imanuel.dev/dockerhub/mailhog---mailhog:latest
      command:
        - sleep
      args:
        - infinity
      ports:
        - containerPort: 1025
'''
            defaultContainer 'php'
        }
    }
    stages {
        stage('Lint and test code') {
            steps {
                sh "mkdir -p /usr/share/man/man1"
                sh "apt-get update"
                sh "apt-get install -y apt-utils"
                sh 'curl -sL https://deb.nodesource.com/setup_current.x -o nodesource_setup.sh'
                sh 'bash nodesource_setup.sh'
                sh "apt-get install -y libzip-dev git wget unzip zip nodejs"
                sh 'npm install -g yarn'
                sh "docker-php-ext-install pdo pdo_mysql zip"
                sh "php --version"
                sh '''php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"'''
                sh "php composer-setup.php"
                sh '''php -r "unlink(\'composer-setup.php\');"'''
                sh 'php composer.phar install'
                dir('designer') {
                    sh 'yarn'
                    sh 'yarn build:prod'
                }
                sh './vendor/bin/psalm'
                sh './vendor/bin/phpstan --no-progress ./src ./tests ./app ./cli ./public'
                sh './vendor/bin/phpunit --log-junit ./report.xml --configuration ./phpunit.jenkins.xml'
            }
        }
        stage('Archive artifact') {
            when {
                branch 'main'
            }
            steps {
                sh "zip -r ./jinya-cms.zip ./* --exclude .git/ --exclude .sonarwork/ --exclude sonar-project.properties"
                archiveArtifacts artifacts: 'jinya-cms.zip', followSymlinks: false, onlyIfSuccessful: true
            }
        }
        stage('Create and publish package') {
            when {
                buildingTag()
            }
            environment {
                JINYA_RELEASES_AUTH = credentials('releases.jinya.de')
            }
            steps {
                container('package') {
                    sh 'apt-get update'
                    sh 'apt-get install zip unzip -y'
                    sh 'zip -r ./jinya-cms.zip ./*'
                    archiveArtifacts artifacts: 'jinya-cms.zip', followSymlinks: false
                    sh 'go run ./main.go'
                }
            }
        }
        stage('Build and push docker image') {
            when {
                buildingTag()
            }
            steps {
                container('docker') {
                    sh "docker build -t quay.imanuel.dev/jinya/jinya-cms:$TAG_NAME -f ./Dockerfile ."
                    sh "docker tag quay.imanuel.dev/jinya/jinya-cms:$TAG_NAME quay.imanuel.dev/jinya/jinya-cms:latest"

                    sh "docker tag quay.imanuel.dev/jinya/jinya-cms:$TAG_NAME jinyacms/jinya-cms:$TAG_NAME"
                    sh "docker tag quay.imanuel.dev/jinya/jinya-cms:$TAG_NAME jinyacms/jinya-cms:latest"

                    withDockerRegistry(credentialsId: 'quay.imanuel.dev', url: 'https://quay.imanuel.dev') {
                        sh "docker push quay.imanuel.dev/jinya/jinya-cms:$TAG_NAME"
                        sh "docker push quay.imanuel.dev/jinya/jinya-cms:latest"
                    }
                    withDockerRegistry(credentialsId: 'hub.docker.com', url: '') {
                        sh "docker push jinyacms/jinya-cms:$TAG_NAME"
                        sh "docker push jinyacms/jinya-cms:latest"
                    }
                }
            }
        }
    }
    post {
        always {
            junit 'report.xml'
            archiveArtifacts artifacts: 'report.xml', followSymlinks: false, onlyIfSuccessful: true
        }
    }
}
