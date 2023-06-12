package models

import "analytic/internal/service/post_service/models"

type Response struct {
	Code int   `json:"code"`
	Time int64 `json:"time_ms"`
}

type Request struct {
	models.PostData
}
