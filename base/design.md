# Model
- get,selectで取得するフィールドの名称を指定できる。
- 指定されなくても、主キーは必ず含まれる。
- validateでは指定したフィールドだけが検証される。
- updateでは指定したフィールドだけが更新される。
# Field
- フィールドの定義を保持する。
    - フィールド名
    - カラム名
    - データ型
    - DBデータ型
    - 最小値
    - 最大値
    - 規定値
    - 参照モデル名
    - 参照モデルID

# ValModel
- モデル単位のバリデータ。
- create,select,update,その他カスタムメソッド用
# ValField
- フィールド種別単位のバリデータ。
    - ValPostal,ValEmail,ValPhoneNoなど
    - 