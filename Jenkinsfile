pipeline {
  agent any
  stages {
    stage('test') {
      steps {
        pmd(canComputeNew: true, canResolveRelativePaths: true, canRunOnFailed: true)
      }
    }
  }
}