pipeline {
    agent any

    triggers {
        pollSCM('H */4 * * 1-5')
    }

    stages {
        stage('Install Dependencies') {
            steps {
                sh 'composer install'
            }
        }

        stage('Test') {
            steps {
                parallel (
                    'Unit Test': {
                        sh 'phpdbg -qrr vendor/bin/phpunit -v --log-junit build/reports/junit.xml  --coverage-clover build/reports/coverage.xml'
                    },
                    'Linting': {
                        sh './script/lint' // Ensure everything is linted
                    }
                )
            }
        }

        stage('SonarQube') {
            environment {
                scannerHome = tool 'SonarQubeScanner'
            }
            steps {
                withSonarQubeEnv('SonarQube') {
                    sh "${scannerHome}/bin/sonar-scanner"
                }
                timeout(time: 10, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Release development') {
            steps {
                script {
                    if (env.BRANCH_NAME == 'master') {
                        build job: '../Release',
                        parameters: [
                            string(name: 'GIT_TARGET', value: 'master'),
                            string(name: 'ENV_TARGET', value: 'development')
                        ],
                        quietPeriod: 0
                    }
                }
            }
        }
    }

    post {
        always {
            junit 'build/reports/junit.xml'
            step([
                $class: 'CloverPublisher',
                cloverReportDir: 'build/reports',
                cloverReportFileName: 'coverage.xml',
                healthyTarget: [methodCoverage: 70, conditionalCoverage: 80, statementCoverage: 80],
                unhealthyTarget: [methodCoverage: 50, conditionalCoverage: 50, statementCoverage: 50],
                failingTarget: [methodCoverage: 0, conditionalCoverage: 0, statementCoverage: 0]
            ])
            cleanWs()
        }
    }
}
