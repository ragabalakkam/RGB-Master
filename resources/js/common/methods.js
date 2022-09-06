/*
**  @input  : input element which is in file type
**  @target : img element to be filled with image in the input
*/

export function fillWithImg(input, target) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      target.attr('data-src', target.attr('src'));
      target.attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}

/*
**  @time  : number (hours/minutes/seconds) to add zero if < 10 .. i.e. 5 => 05
**  returns null if time equals to null
*/

export function CastTimeZeros(time) {
  return time < 10 ? "0" + time : time;
}

/*
**  @hours, miutes to cast into AM or PM
*/

export function CastDayNight(hours, miuntes) {
  let dayNight = "AM";
  if (hours >= 12) {
    hours -= 12;
    dayNight = "PM";
  }
  if (!hours) hours = 12;
  return [`${CastTimeZeros(hours)}:${CastTimeZeros(miuntes)}`, dayNight];
}

/*
**  @time  : time in Carbon/Carbon format 
**  returns null if time equals to null
*/

export function CastTime(time = new Date()) {

  if (!time) return null;

  time = new Date(time);

  var today = new Date();
  var local = today.getTime();
  var offset = today.getTimezoneOffset() * (60 * 1000);
  var utc = new Date(local + offset);
  today = new Date(utc.getTime() + (3 * 60 * 60 * 1000));

  const weekday = new Array(
    "Sun",
    "Mon",
    "Tus",
    "Wed",
    "Thu",
    "Fri",
    "Sat"
  );

  const yearMonth = new Array(
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "May",
    "Jun",
    "Jul",
    "Aug",
    "Sep",
    "Oct",
    "Nov",
    "Dec"
  );

  const d = time.getDate();
  const m = time.getMonth() + 1;
  const y = time.getFullYear();

  let day = "";

  switch (d) {
    case today.getDate():
      day = "today";
      break;

    case today.getDate() - 1:
      day = "yesterday";
      break;

    default:
      day = weekday[time.getDay()];
  }

  const x = CastDayNight(time.getHours(), time.getMinutes());

  return {
    d,
    m,
    y,
    day,
    month:  yearMonth[m - 1],
    time:  x[0],
    am_pm: x[1],
};
}

/*
** Debugging
*/
export function printFormData(formdata) {
  for (var pair of formdata.entries()) {
    console.log(pair[0] + ' - ' + pair[1]);
  }
}

/*
** Degree to Radian
*/

export function rad(angle) {
  return (angle * Math.PI) / 180;
}

/* text-emphasis */

export function ucFirst(string) {
  string = string.replace('-', ' ').toLowerCase();
  return string[0].toUpperCase() + string.substring(1);
}

export function capitalize(string, splitter = ' ') {
  string = string.replace(' ', '-').split('-');
  string.forEach(word => ucFirst(word));
  return string.join(splitter);
}