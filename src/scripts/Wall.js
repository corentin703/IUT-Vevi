function initializeLikeButton($like) {

    console.log($like);

    if ($like['like'] == true)
        document.getElementById('likeType').id = 'tempered';
    else
        document.getElementById('likeType').id = "unTempered";


    if ($like['dislike'] == true)
        document.getElementById('likeType').id = 'tempered';
    else
        document.getElementById('likeType').id = "unTempered";
}

function initializeFollowButton($follow) {

    if ($follow == true)
        document.getElementById('followType').id = 'tempered';
    else
        document.getElementById('followType').id = "unTempered";
}

// function uriReset()
// {
//     // window.location.replace('/home');
//     history.pushState('wall', 'VÉVI - Home', '/home');
// }