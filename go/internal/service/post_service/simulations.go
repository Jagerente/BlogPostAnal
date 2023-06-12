package post_service

import (
	"analytic/internal/service/post_service/models"
	"errors"
	"math/rand"
	"time"

	"go.uber.org/zap"
)

func (s *PostService) somethingHeavy(data models.PostData, ch chan error) {
	var logger = s.logger.With(
		zap.String("function", "somethingHeavy"),
	)

	// Simulate delay
	logger.Debug("Starting throttling simulation")
	s.simulateThrottling()

	// Simulate random error
	logger.Debug("Starting error simulation")
	if err := s.simulateError(data.PostId%3 == 0); err != nil {
		ch <- err
		return
	}

	ch <- nil
}

func (s *PostService) simulateError(conditions ...bool) error {
	var logger = s.logger.With(
		zap.String("function", "simulateError"),
	)

	if rand.Intn(10) == 0 || anyConditionTrue(conditions) {
		logger.Debug("error passed")
		return errors.New("random error")
	}

	logger.Debug("error not passed")
	return nil
}

func (s *PostService) simulateThrottling() {
	var logger = s.logger.With(
		zap.String("function", "simulateThrottling"),
	)

	var duration = randomDuration(3, 15)

	logger.Sugar().Debugf("Throttling for %s", duration)

	time.Sleep(duration)
}

func anyConditionTrue(conditions []bool) bool {
	for _, condition := range conditions {
		if condition {
			return true
		}
	}
	return false
}

func randomDuration(min, max int) time.Duration {
	source := rand.NewSource(time.Now().UnixNano())
	random := rand.New(source)
	seconds := random.Intn(max-min+1) + min
	return time.Duration(seconds) * time.Second
}
