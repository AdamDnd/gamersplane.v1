controllers.controller('user', ['$scope', '$http', 'CurrentUser', 'UsersService', function ($scope, $http, CurrentUser, UsersService) {
	$scope.$emit('pageLoading');
	CurrentUser.load().then(function () {
		$scope.user = null;
		$scope.systems = {};
		$scope.profileFields = { 'location': 'Location', 'aim': 'AIM', 'yahoo': 'Yahoo!', 'msn': 'MSN' };
		$scope.userChart = {last12Months:[], chartBoxSize:10};
		var today=new Date();
		for(var i=0;i<12;i++){
			var addMonth = new Date(today.getFullYear(), today.getMonth()-11+i, 1);
			$scope.userChart.last12Months.push({y:addMonth.getFullYear(),m:addMonth.getMonth()+1,n:0,monthName:moment(addMonth).format("MMM")});
		}

		pathElements = getPathElements();
		userID = null;
		if (!isUndefined(pathElements[1]))
			userID = parseInt(pathElements[1]);
		UsersService.get(userID).then(function (data) {
			if (data) {
				$scope.user = data;
				$scope.user.lastInactivity = UsersService.inactive($scope.user.lastActivity, false);
				$scope.user.lastActivity = lastActiveText($scope.user.lastActivity);
				$http.post(API_HOST + '/users/stats/', { userID: userID }).then(function (response) {
					$scope.characters = response.data.characters.list;
					$scope.posts = {
						postCount: response.data.posts.count,
						communityPostCount: response.data.posts.communityCount,
						gamePostCount: response.data.posts.gameCount
					};
					$scope.charCount = response.data.characters.numChars;
					$scope.characters.forEach(function (ele) {
						ele.percentage = Math.round(ele.numChars / $scope.charCount * 100);
					});
					$scope.games = response.data.games.list;
					$scope.gameCount = response.data.games.numGames;
					$scope.games.forEach(function (ele) {
						ele.percentage = Math.round(ele.numGames / $scope.gameCount * 100);
					});
					$scope.activeGames = response.data.activeGames;

					var maxVal=0;
					for(var i=0;i<$scope.userChart.last12Months.length;i++){
						for(var j=0;j<$scope.user.userActivity.length;j++){
							if($scope.userChart.last12Months[i].m==$scope.user.userActivity[j].m && $scope.userChart.last12Months[i].y==$scope.user.userActivity[j].y){
								$scope.userChart.last12Months[i].n=$scope.user.userActivity[j].n;
								maxVal=Math.max(maxVal,$scope.user.userActivity[j].n);
								break;
							}
						}
					}

					if(maxVal<100){
						$scope.userChart.chartBoxSize=1;
					} else if(maxVal<500){
						$scope.userChart.chartBoxSize=5;
					} else if(maxVal<1000){
						$scope.userChart.chartBoxSize=10;
					} else {
						$scope.userChart.chartBoxSize=10;
					}

				});
				$scope.$emit('pageLoading');
			}
		});
	});

	var lastActiveText = function(lastActivity){
		if (typeof lastActivity == 'number')
			lastActivity *= 1000;
		lastActivity = moment(lastActivity);
		var now = moment();
		var diff = now - lastActivity;
		var diffSeconds = Math.floor(diff / 1000);
		diff = Math.floor(diffSeconds / (60 * 60 * 24));
		if (diff < 14)
		{
			if(diffSeconds<=86400){
				return "< 1 day ago";
			} else if(diffSeconds<=(86400*2)){
				return "1 day ago";
			} else {
				return diff + " days ago";
			}
		}

		return null;
	};

}]);