const siteConfig = require(process.cwd() + '/../siteConfig.js');



function initMap() {
  // Styles a map in night mode.
	var cities = [
		{
	    "municipio": "São Miguel dos Campos",
	    "cod_ibge": 2708600,
	    "latitude": -9.78301,
	    "longitude": -36.0971
	  },
	  {
	    "municipio": "Escola Calabar",
	    "cod_ibge": 2927408,
	    "latitude": -12.9718,
	    "longitude": -38.5011
	  },
	  {
	    "municipio": "Viçosa",
	    "cod_ibge": 3171303,
	    "latitude": -20.7559,
	    "longitude": -42.8742
	  },
	  {
	    "municipio": "Instituto Mãos de arte - IMA",
	    "cod_ibge": 5300108,
	    "latitude": -15.7795,
	    "longitude": -47.9297
	  },
	  {
	    "municipio": "Brasília",
	    "cod_ibge": 5300108,
	    "latitude": -15.7795,
	    "longitude": -47.9297
	  },
	  {
	    "municipio": "Valparaíso de Goiás",
	    "cod_ibge": 5221858,
	    "latitude": -16.0651,
	    "longitude": -47.9757
	  },
	  {
	    "municipio": "São Pedro da Água Branca",
	    "cod_ibge": 2111532,
	    "latitude": -5.08472,
	    "longitude": -48.4291
	  },
	  {
	    "municipio": "Diamantino",
	    "cod_ibge": 5103502,
	    "latitude": -14.4037,
	    "longitude": -56.4366
	  },
	  {
	    "municipio": "Nortelândia",
	    "cod_ibge": 5106000,
	    "latitude": -14.454,
	    "longitude": -56.7945
	  },
	  {
	    "municipio": "Querência",
	    "cod_ibge": 5107065,
	    "latitude": -12.6093,
	    "longitude": -52.1821
	  },
	  {
	    "municipio": "Benevides",
	    "cod_ibge": 1501501,
	    "latitude": -1.36183,
	    "longitude": -48.2434
	  },
	  {
	    "municipio": "Dom Eliseu",
	    "cod_ibge": 1502939,
	    "latitude": -4.1994424,
	    "longitude": -47.8245049
	  },
	  {
	    "municipio": "Paragominas",
	    "cod_ibge": 1505502,
	    "latitude": -3.00212,
	    "longitude": -47.3527
	  },
	  {
	    "municipio": "Parauapebas",
	    "cod_ibge": 1505536,
	    "latitude": -6.06781,
	    "longitude": -49.9037
	  },
	  {
	    "municipio": "Rondon do Pará",
	    "cod_ibge": 1506187,
	    "latitude": -4.77793,
	    "longitude": -48.067
	  },
	  {
	    "municipio": "Ipiranga do Piauí",
	    "cod_ibge": 2204808,
	    "latitude": -6.82421,
	    "longitude": -41.7381
	  },
	  {
	    "municipio": "Morro do Chapéu",
	    "cod_ibge": 2921708,
	    "latitude": -11.5488,
	    "longitude": -41.1565
	  },
	  {
	    "municipio": "Resende",
	    "cod_ibge": 3304201,
	    "latitude": -22.4705,
	    "longitude": -44.4509
	  },
	  {
	    "municipio": "Campo Redondo",
	    "cod_ibge": 2402105,
	    "latitude": -6.23829,
	    "longitude": -36.1888
	  },
	  {
	    "municipio": "Caraúbas",
	    "cod_ibge": 2402303,
	    "latitude": -5.78387,
	    "longitude": -37.5586
	  },
	  {
	    "municipio": "Doutor Severiano",
	    "cod_ibge": 2403202,
	    "latitude": -6.08082,
	    "longitude": -38.3794
	  },
	  {
	    "municipio": "Ipanguaçu",
	    "cod_ibge": 2404705,
	    "latitude": -5.48984,
	    "longitude": -36.8501
	  },
	  {
	    "municipio": "Itajá",
	    "cod_ibge": 5210802,
	    "latitude": -19.0673,
	    "longitude": -51.5495
	  },
	  {
	    "municipio": "Monte Alegre",
	    "cod_ibge": 1504802,
	    "latitude": -1.99768,
	    "longitude": -54.0724
	  },
	  {
	    "municipio": "Nova Cruz",
	    "cod_ibge": 2408300,
	    "latitude": -6.47511,
	    "longitude": -35.4286
	  },
	  {
	    "municipio": "Parazinho",
	    "cod_ibge": 2408805,
	    "latitude": -5.22276,
	    "longitude": -35.8398
	  },
	  {
	    "municipio": "Patu",
	    "cod_ibge": 2409308,
	    "latitude": -6.10656,
	    "longitude": -37.6356
	  },
	  {
	    "municipio": "Santo Antônio",
	    "cod_ibge": 2411502,
	    "latitude": -6.31195,
	    "longitude": -35.4739
	  },
	  {
	    "municipio": "Vera Cruz",
	    "cod_ibge": 3556602,
	    "latitude": -22.2183,
	    "longitude": -49.8207
	  },
	  {
	    "municipio": "Chupinguaia",
	    "cod_ibge": 1100924,
	    "latitude": -12.5611,
	    "longitude": -60.8877
	  },
	  {
	    "municipio": "Theobroma",
	    "cod_ibge": 1101609,
	    "latitude": -10.2483,
	    "longitude": -62.3538
	  },
	  {
	    "municipio": "Araranguá",
	    "cod_ibge": 4201406,
	    "latitude": -28.9356,
	    "longitude": -49.4918
	  },
	  {
	    "municipio": "Balneário Arroio do Silva",
	    "cod_ibge": 4201950,
	    "latitude": -28.9806,
	    "longitude": -49.4237
	  },
	  {
	    "municipio": "Balneário Camboriú",
	    "cod_ibge": 4202008,
	    "latitude": -26.9926,
	    "longitude": -48.6352
	  },
	  {
	    "municipio": "Balneário Gaivota",
	    "cod_ibge": 4202073,
	    "latitude": -29.1527,
	    "longitude": -49.5841
	  },
	  {
	    "municipio": "Caçador",
	    "cod_ibge": 4203006,
	    "latitude": -26.7757,
	    "longitude": -51.012
	  },
	  {
	    "municipio": "Cocal do Sul",
	    "cod_ibge": 4204251,
	    "latitude": -28.5986,
	    "longitude": -49.3335
	  },
	  {
	    "municipio": "Criciúma",
	    "cod_ibge": 4204608,
	    "latitude": -28.6723,
	    "longitude": -49.3729
	  },
	  {
	    "municipio": "Grão Pará",
	    "cod_ibge": 4206108,
	    "latitude": -28.1809,
	    "longitude": -49.2252
	  },
	  {
	    "municipio": "Içara",
	    "cod_ibge": 4207007,
	    "latitude": -28.7132,
	    "longitude": -49.3087
	  },
	  {
	    "municipio": "Jacinto Machado",
	    "cod_ibge": 4208708,
	    "latitude": -28.9961,
	    "longitude": -49.7623
	  },
	  {
	    "municipio": "Jaguaruna",
	    "cod_ibge": 4208807,
	    "latitude": -28.6146,
	    "longitude": -49.0296
	  },
	  {
	    "municipio": "Laguna",
	    "cod_ibge": 4209409,
	    "latitude": -28.4843,
	    "longitude": -48.7772
	  },
	  {
	    "municipio": "Lebon Régis",
	    "cod_ibge": 4209706,
	    "latitude": -26.928,
	    "longitude": -50.6921
	  },
	  {
	    "municipio": "Mafra",
	    "cod_ibge": 4210100,
	    "latitude": -26.1159,
	    "longitude": -49.8086
	  },
	  {
	    "municipio": "Maracajá",
	    "cod_ibge": 4210407,
	    "latitude": -28.8463,
	    "longitude": -49.4605
	  },
	  {
	    "municipio": "Meleiro",
	    "cod_ibge": 4210803,
	    "latitude": -28.8244,
	    "longitude": -49.6378
	  },
	  {
	    "municipio": "Navegantes",
	    "cod_ibge": 4211306,
	    "latitude": -26.8943,
	    "longitude": -48.6546
	  },
	  {
	    "municipio": "Nova Veneza",
	    "cod_ibge": 4211603,
	    "latitude": -28.6338,
	    "longitude": -49.5055
	  },
	  {
	    "municipio": "Ouro Verde",
	    "cod_ibge": 4211850,
	    "latitude": -26.692,
	    "longitude": -52.3108
	  },
	  {
	    "municipio": "Passo de Torres",
	    "cod_ibge": 4212254,
	    "latitude": -29.3099,
	    "longitude": -49.722
	  },
	  {
	    "municipio": "Pescaria Brava",
	    "cod_ibge": 4212650,
	    "latitude": -28.3966,
	    "longitude": -48.8864
	  },
	  {
	    "municipio": "Praia Grande",
	    "cod_ibge": 4213807,
	    "latitude": -29.1918,
	    "longitude": -49.9525
	  },
	  {
	    "municipio": "Rio Negrinho",
	    "cod_ibge": 4215000,
	    "latitude": -26.2591,
	    "longitude": -49.5177
	  },
	  {
	    "municipio": "Sangão",
	    "cod_ibge": 4215455,
	    "latitude": -28.6326,
	    "longitude": -49.1322
	  },
	  {
	    "municipio": "Santa Rosa do Sul",
	    "cod_ibge": 4215653,
	    "latitude": -29.1313,
	    "longitude": -49.7109
	  },
	  {
	    "municipio": "Schroeder",
	    "cod_ibge": 4217402,
	    "latitude": -26.4116,
	    "longitude": -49.074
	  },
	  {
	    "municipio": "Sombrio",
	    "cod_ibge": 4217709,
	    "latitude": -29.108,
	    "longitude": -49.6328
	  },
	  {
	    "municipio": "Timbé do Sul",
	    "cod_ibge": 4218103,
	    "latitude": -28.8287,
	    "longitude": -49.842
	  },
	  {
	    "municipio": "Botucatu",
	    "cod_ibge": 3507506,
	    "latitude": -22.8837,
	    "longitude": -48.4437
	  },
	  {
	    "municipio": "Campos do Jordão",
	    "cod_ibge": 3509700,
	    "latitude": -22.7296,
	    "longitude": -45.5833
	  },
	  {
	    "municipio": "Jacareí",
	    "cod_ibge": 3524402,
	    "latitude": -23.2983,
	    "longitude": -45.9658
	  },
	  {
	    "municipio": "Piracicaba",
	    "cod_ibge": 3538709,
	    "latitude": -22.7338,
	    "longitude": -47.6476
	  }
	]
  var map = new google.maps.Map(document.getElementById('map'), {
    center: {
      lat: -12.258204,
      lng: -50.896296
    },
    zoom: 4,
    styles: [{
        elementType: 'geometry',
        stylers: [{
          color: '#2792ff'
        }]
      },
      {
        elementType: 'geometry.stroke',
        stylers: [{
          color: '#ffffff'
        }]
      },
      {
        elementType: 'labels.text.stroke',
        stylers: [{
          color: '#333333'
        }]
      },
      {
        elementType: 'labels.text.fill',
        stylers: [{
          color: '#ffffff'
        }]
      },
      {
        featureType: 'road',
        elementType: 'geometry',
        stylers: [{
          color: '#3ee4cf'
        }]
      },
      {
        featureType: 'road',
        elementType: 'geometry.stroke',
        stylers: [{
          color: '#8101f7'
        }]
      },
      {
        featureType: 'road',
        elementType: 'labels.text.fill',
        stylers: [{
          color: '#9ca5b3'
        }, {
          'visibility': 'off'
        }]
      },
      {
        featureType: 'road.highway',
        elementType: 'geometry',
        stylers: [{
          color: '#3ee4cf'
        }, {
          'visibility': 'off'
        }]
      },
      {
        featureType: 'road.highway',
        elementType: 'geometry.stroke',
        stylers: [{
          color: '#8101f7'
        }, {
          'visibility': 'off'
        }]
      },
      {
        featureType: 'road.highway',
        elementType: 'labels.text.fill',
        stylers: [{
          color: '#3ee4cf'
        }, {
          'visibility': 'off'
        }]
      },
      {
        featureType: 'water',
        elementType: 'geometry',
        stylers: [{
          color: '#17263c'
        }]
      },
      {
        featureType: 'water',
        elementType: 'labels.text.fill',
        stylers: [{
          color: '#515c6d'
        }]
      },
      {
        featureType: 'water',
        elementType: 'labels.text.stroke',
        stylers: [{
          color: '#17263c'
        }]
      }
    ]
  });
  var markers = cities.map(function(city, i) {
    return new google.maps.Marker({
      position: {
        lat: city.latitude,
        lng: city.longitude
      },
      map: map,
      icon: {
	      url: "https://svgshare.com/i/7XS.svg",
        scaledSize: new google.maps.Size(32, 32),
      }
    });
  });


}
