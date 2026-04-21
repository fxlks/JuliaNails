let usedColors = []
let colors = [
  '1788FB',
  '4BBEC6',
  'FBC22D',
  'FA3C52',
  'D696B8',
  '689BCA',
  '26CC2B',
  'FD7E35',
  'E38587',
  '774DFB',
  '31CDF3',
  '6AB76C',
  'FD5FA1',
  'A697C5'
]

function usePictureLoad (baseUrls, entity, isPerson) {
  if (entity !== null) {
    let name = isPerson === true ? entity.firstName + ' ' + entity.lastName : entity.name
    if (typeof name !== 'undefined') {
      entity.pictureThumbPath = entity.pictureThumbPath || imageFromText(baseUrls, name)
      return entity.pictureThumbPath
    }
  }
}

function  getNameInitials (name) {
  return name.split(' ').map((s) => s.charAt(0)).join('').toUpperCase().substring(0, 3).replace(/[^\w\s]/g, '')
}

function imageFromText (baseUrls, name, entity = {}, error = false) {
  let initials = getNameInitials(name)
  let colorIndex = Math.floor(Math.random() * colors.length)
  let colorHex = colors[colorIndex]
  usedColors.push(colors[colorIndex])
  colors.splice(colorIndex, 1)
  if (colors.length === 0) {
    colors = usedColors
    usedColors = []
  }
  if (error) {
    if (entity.firstName) {
      return baseUrls.wpAmeliaPluginURL + 'public/img/default-employee.svg'
    }
    if (entity.latitude) {
      return baseUrls.wpAmeliaPluginURL + 'public/img/default-location.svg'
    }
    return baseUrls.wpAmeliaPluginURL + 'public/img/default-service.svg'
  }

  // Make canvas
  const canvas = document.createElement('canvas');
  canvas.width = 100;
  canvas.height = 100;
  const ctx = canvas.getContext('2d');

  // Draw background color
  ctx.fillStyle = `#${colorHex}` // Background color
  ctx.fillRect(0, 0, canvas.width, canvas.height);

  // Draw initials text
  ctx.font = '40px Arial';
  ctx.fillStyle = '#ffffff'; // Text color
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText(initials, canvas.width / 2, canvas.height / 2);

  // Convert canvas to data URL (as image source)
  return canvas.toDataURL('image/png');
}

export {
  usePictureLoad,
  getNameInitials
}
