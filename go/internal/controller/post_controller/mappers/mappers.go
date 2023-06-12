package mappers

import (
	"analytic/internal/controller/post_controller/models"
	s_models "analytic/internal/service/post_service/models"
)

func MapToService(model models.Request) s_models.PostData {
	return model.PostData
}
