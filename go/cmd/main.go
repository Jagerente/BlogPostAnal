package main

import (
	"analytic/internal/controller/middlewares"
	"analytic/internal/controller/post_controller"
	"analytic/internal/core"
	"analytic/internal/db"
	"analytic/internal/db/repositories"
	"analytic/internal/service/post_service"
	"analytic/pkg/configuration"
	"net/http"
	"time"

	"github.com/gin-contrib/cors"
	"github.com/gin-gonic/gin"
	"go.uber.org/zap"
	"gorm.io/gorm"
)

var (
	logger *zap.Logger
	orm    *gorm.DB
	env    Config

	postController post_controller.IPostController
)

type Config struct {
	DBHost         string `mapstructure:"POSTGRES_HOST"`
	DBName         string `mapstructure:"POSTGRES_DB"`
	DBUserName     string `mapstructure:"POSTGRES_USER"`
	DBUserPassword string `mapstructure:"POSTGRES_PASSWORD"`
	DBPort         uint16 `mapstructure:"POSTGRES_CONTAINER_PORT"`
	ServerPort     string `mapstructure:"SERVER_PORT"`
	LogLevel       int8   `mapstructure:"LOG_LEVEL"`
	GinMode        string `mapstructure:"GIN_MODE"`
	SecretKey      string `mapstructure:"SECRET_KEY"`
}

func init() {
	// Init ENV
	cfg, err := configuration.New[Config]()
	if err != nil {
		panic(err)
	}

	env = cfg.ENV

	// Init Logger
	logger = configuration.GetLogger(env.LogLevel)

	// Init DB
	var dbConfig *db.PostgresDatabaseConfiguration = &db.PostgresDatabaseConfiguration{
		Host:         env.DBHost,
		UserName:     env.DBUserName,
		UserPassword: env.DBUserPassword,
		DatabaseName: env.DBName,
		Port:         env.DBPort,
	}
	orm, err = db.InitializePostgresDatabase(dbConfig)
	if err != nil {
		logger.Panic(err.Error())
	}

	// Get repo provider
	provider := repositories.CreateProvider(orm)

	// Init core
	core := core.CreateCore(logger, provider)

	// Get controllers
	postController = post_controller.CreatePostController(post_service.CreatePostService(core), core.GetLogger())
}

func main() {
	// Configre GIN
	r := gin.Default()

	// Set gin mode
	gin.SetMode(env.GinMode)

	// Configure CORS
	r.Use(cors.New(cors.Config{
		AllowAllOrigins: true,
		AllowMethods:    []string{"GET"},
		AllowHeaders:    []string{"Origin", "Content-Length", "Content-Type", "Accept-Languages"},
		MaxAge:          12 * time.Hour,
	}))

	// Set main route
	mainRoute := r.Group("/api")

	// Set routes
	posts := mainRoute.Group("/posts")
	{
		posts.POST("/", middlewares.Authenticate(env.SecretKey), middlewares.LogRawData(logger), postController.Send)
	}

	// In case of wrong route
	r.NoRoute(func(c *gin.Context) {
		c.JSON(http.StatusNotFound, gin.H{
			"message": "Not Found",
		})
	})

	// Run server
	err := r.Run(":" + env.ServerPort)
	if err != nil {
		logger.Panic(err.Error())
	}
}
