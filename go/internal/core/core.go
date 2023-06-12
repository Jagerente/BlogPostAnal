package core

import (
	"analytic/internal/db/repositories"

	"go.uber.org/zap"
)

type Core struct {
	logger *zap.Logger
	repos  repositories.IRepositoryProvider
}

type ICore interface {
	GetLogger() *zap.Logger
	GetRepositories() repositories.IRepositoryProvider
}

func CreateCore(logger *zap.Logger, repos repositories.IRepositoryProvider) *Core {
	return &Core{
		logger: logger,
		repos:  repos,
	}
}

func (core *Core) GetLogger() *zap.Logger {
	return core.logger
}

func (core *Core) GetRepositories() repositories.IRepositoryProvider {
	return core.repos
}
