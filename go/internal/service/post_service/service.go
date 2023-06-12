package post_service

import (
	"analytic/internal/core"
	"analytic/internal/service/post_service/mappers"
	"analytic/internal/service/post_service/models"

	"go.uber.org/zap"
)

type PostService struct {
	core   core.ICore
	logger *zap.Logger
}

type IPostService interface {
	RegisterEvent(models.PostData) error
}

func CreatePostService(core core.ICore) *PostService {
	var log = core.GetLogger().With(
		zap.String("service", "post_service"),
	)

	return &PostService{
		core:   core,
		logger: log,
	}
}

func (s *PostService) RegisterEvent(data models.PostData) error {
	var logger = s.logger.With(
		zap.String("function", "RegisterEvent"),
	)

	logger.Debug("starting do something very heavy in gorutine")
	var ch = make(chan error)
	go s.somethingHeavy(data, ch)
	if err := <-ch; err != nil {
		logger.Debug("finished doing something with error")
		return err
	}
	logger.Debug("finished doing something without error")

	// Get repository
	logger.Debug("getting repository")
	repo := s.core.GetRepositories().CreatePostChangesRepo()

	// Map to db data
	logger.Debug("mapping to database",
		zap.Any("data", data),
	)
	dbData := mappers.MapToDb(data)
	logger.Debug("adding to database",
		zap.Any("mappedData", dbData),
	)

	// Add to database
	_, err := repo.Add(&dbData)
	if err != nil {
		return err
	}
	logger.Debug("added to database",
		zap.Any("dbData", dbData),
	)

	logger.Debug("validated successfully")

	return nil
}
