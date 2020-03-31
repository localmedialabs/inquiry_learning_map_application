var assetPath = {}
assetPath['common'] = {}
assetPath['common']['loading'] = require('@/assets/common/loading.gif')
assetPath['common']['title'] = require('@/assets/common/title.png')
assetPath['common']['menu'] = require('@/assets/common/menu.png')
assetPath['common']['noimage'] = require('@/assets/common/noimage.png')
assetPath['startup'] = {}
assetPath['startup']['startup'] = require('@/assets/startup/startup.png')
assetPath['menu'] = {}
assetPath['menu']['disaster'] = require('@/assets/menu/disaster.png')
assetPath['menu']['everyoneMap'] = require('@/assets/menu/everyoneMap.png')
assetPath['menu']['facilities'] = require('@/assets/menu/facilities.png')
assetPath['menu']['map'] = require('@/assets/menu/map.png')
assetPath['menu']['setting'] = require('@/assets/menu/setting.png')
assetPath['menu']['test'] = require('@/assets/menu/test.png')
assetPath['menu']['teacher'] = require('@/assets/menu/teacher.png')
assetPath['basemap'] = {}
assetPath['basemap']['map'] = require('@/assets/basemap/map.png')
assetPath['dataset'] = {}
assetPath['dataset']['AED'] = require('@/assets/dataset/aed.png')
assetPath['dataset']['EMERGENCYSHELTER'] = require('@/assets/dataset/emergency_shelter.png')
assetPath['dataset']['MEDICAL'] = require('@/assets/dataset/medical.png')
assetPath['dataset']['teacher'] = require('@/assets/dataset/teacher.png')
assetPath['dataset']['COMMON'] = require('@/assets/dataset/teacher.png')
assetPath['dataset']['regist'] = require('@/assets/dataset/regist.png')
assetPath['disaster'] = {}
assetPath['disaster']['kasai'] = require('@/assets/disaster/kasai.png')
assetPath['disaster']['dosekiryuu'] = require('@/assets/disaster/dosekiryuu.png')
assetPath['disaster']['jisuberi'] = require('@/assets/disaster/jisuberi.png')
assetPath['disaster']['kouzui'] = require('@/assets/disaster/kouzui.png')
assetPath['disaster']['tunami'] = require('@/assets/disaster/tunami.png')
assetPath['map'] = {}
assetPath['map']['information'] = require('@/assets/map/information.png')
assetPath['map']['edit_circle'] = require('@/assets/map/edit_circle.png')
assetPath['map']['edit_line'] = require('@/assets/map/edit_line.png')
assetPath['reviewtest'] = {}
assetPath['reviewtest']['marker'] = require('@/assets/reviewtest/marker.png')
assetPath['reviewtest']['contents'] = require('@/assets/reviewtest/contents.png')

/**
 * 画像操作共通部品クラス
 *
 * @class 画像操作共通部品クラス
 * @param なし
 */
export default class assetHelper {
  getAssetData () {
    return assetPath
  }
}
