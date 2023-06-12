package configuration

import (
	"go.uber.org/zap"
)

func GetLogger(logLevel int8) *zap.Logger {
	logger, _ := zap.NewProduction()

	if logLevel == 0 {
		logger, _ = zap.NewDevelopment()
	}

	return logger
}
