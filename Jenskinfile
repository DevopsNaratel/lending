pipeline {
    agent any

    environment {
        // --- KONFIGURASI DOCKER ---
        DOCKER_IMAGE = 'diwamln/florist-landing' 
        DOCKER_CREDS = 'docker-hub' 
        
        // --- KONFIGURASI GIT (REPO MANIFEST) ---
        GIT_CREDS    = 'git-token'
        MANIFEST_REPO_URL = 'github.com/DevopsNaratel/deployment-manifests' 
        
        // --- PATH FILE MANIFEST ---
        // Pastikan file deployment.yaml ini sudah ada di repo manifest Anda
        MANIFEST_TEST_PATH = 'florist-landing/dev/deployment.yaml'
        MANIFEST_PROD_PATH = 'florist-landing/prod/deployment.yaml' 
    }

    stages {
        stage('Checkout & Versioning') {
            steps {
                checkout scm
                script {
                    // Membuat tag unik berdasarkan nomor build dan hash commit
                    def commitHash = sh(returnStdout: true, script: "git rev-parse --short HEAD").trim()
                    env.BASE_TAG = "build-${BUILD_NUMBER}-${commitHash}" 
                    currentBuild.displayName = "#${BUILD_NUMBER} (${env.BASE_TAG})"
                }
            }
        }

        stage('Build & Push Docker Image') {
            steps {
                script {
                    docker.withRegistry('', DOCKER_CREDS) {
                        echo "Building Image: ${DOCKER_IMAGE}:${env.BASE_TAG}"
                        
                        // Build murni tanpa --build-arg karena tidak ada API URL
                        def appImage = docker.build("${DOCKER_IMAGE}:${env.BASE_TAG}")
                        appImage.push()
                        
                        // Opsional: Push tag latest agar selalu terupdate
                        appImage.push('latest')
                    }
                }
            }
        }

        stage('Update Manifest (DEV/TEST)') {
            steps {
                script {
                    sh 'rm -rf temp_manifests'
                    dir('temp_manifests') {
                        withCredentials([usernamePassword(credentialsId: GIT_CREDS, usernameVariable: 'GIT_USER', passwordVariable: 'GIT_PASS')]) {
                            
                            sh "git clone https://${GIT_USER}:${GIT_PASS}@${MANIFEST_REPO_URL} ."
                            sh 'git config user.email "jenkins@bot.com"'
                            sh 'git config user.name "Jenkins Pipeline"'
                            
                            // Mengupdate tag image di file deployment.yaml
                            sh "sed -i -E 's|image: (docker.io/)?${DOCKER_IMAGE}:.*|image: docker.io/${DOCKER_IMAGE}:${env.BASE_TAG}|g' ${MANIFEST_TEST_PATH}"
                            
                            sh """
                                git add .
                                if ! git diff-index --quiet HEAD; then
                                    git commit -m 'Deploy florist-landing: ${env.BASE_TAG} [skip ci]'
                                    git push origin main
                                else
                                    echo "No changes detected."
                                fi
                            """
                        }
                    }
                }
            }
        }

        stage('Approval for Prod') {
            steps {
                input message: "Aplikasi sudah di-update di DEV. Lanjut update manifest PROD?", ok: "Promote to Prod"
            }
        }

        stage('Update Manifest (PROD)') {
            steps {
                script {
                    dir('temp_manifests') {
                        withCredentials([usernamePassword(credentialsId: GIT_CREDS, usernameVariable: 'GIT_USER', passwordVariable: 'GIT_PASS')]) {
                            sh "git pull origin main"
                            
                            // Mengupdate file di folder prod
                            sh "sed -i -E 's|image: (docker.io/)?${DOCKER_IMAGE}:.*|image: docker.io/${DOCKER_IMAGE}:${env.BASE_TAG}|g' ${MANIFEST_PROD_PATH}"
                            
                            sh """
                                git add .
                                if ! git diff-index --quiet HEAD; then
                                    git commit -m 'Promote florist-landing to PROD: ${env.BASE_TAG} [skip ci]'
                                    git push origin main
                                else
                                    echo "No changes detected."
                                fi
                            """
                        }
                    }
                }
            }
        }
    }
}
